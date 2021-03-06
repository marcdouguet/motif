<?php
/**
Génère les formats détachés et le site statique basique sur Œuvres
 */
// cli usage
Teinte_Build::deps();
set_time_limit(-1);


if (realpath($_SERVER['SCRIPT_FILENAME']) != realpath(__FILE__)) {
  // file is included do nothing
}
else if (php_sapi_name() == "cli") {
  Teinte_Build::cli();
}
class Teinte_Build
{
  static $kindlegen;
  static $formats = array(
    'epub' => '.epub',
    'kindle' => '.mobi',
    'markdown' => '.md',
    'iramuteq' => '.txt',
    'html' => '.html',
    'article' => '.html',
    // 'docx' => '.docx',
  );
  /** petite persistance sqlite pour conserver la mémoire des doublons etc */
  static $create = "
PRAGMA encoding = 'UTF-8';
PRAGMA page_size = 8192;

CREATE TABLE oeuvre (
  -- un texte
  id         INTEGER, -- rowid auto
  code       TEXT,    -- nom de fichier sans extension
  filemtime  INTEGER, -- date de dernière modification du fichier pour update
  publisher  TEXT,    -- nom de l’institution qui publie
  identifier TEXT,    -- uri chez le publisher
  source     TEXT,    -- XML TEI source URL
  author     TEXT,    -- auteur
  title      TEXT,    -- titre
  year       INTEGER, -- année de publication
  PRIMARY KEY(id ASC)
);
CREATE UNIQUE INDEX oeuvre_code ON oeuvre(code);
CREATE INDEX oeuvre_author_year ON oeuvre(author, year, title);
CREATE INDEX oeuvre_year_author ON oeuvre(year, author, title);

  ";
  /** SQLite link, maybe useful outside */
  public $pdo;
  /** Insert query for a file */
  private $_insert;
  /** Test de date d’une pièce */
  private $_sqlmtime;
  /** Processeur xslt */
  private $_xslt;
  /** Vrai si dépendances vérifiées et chargées */
  private static $_deps;
  /** A logger, maybe a stream or a callable, used by self::log() */
  private static $_logger;
  /** Log level */
  public static $debug = true;
  /**
   * Constructeur de la base
   */
  public function __construct($sqlitefile, $logger="php://output") {
    if (is_string($logger)) $logger = fopen($logger, 'w');
    self::$_logger = $logger;
    $this->connect($sqlitefile);
    // create needed folders
    /*
    foreach (self::$formats as $format => $extension) {
      if (!file_exists($dir = dirname(__FILE__).'/'.$format)) {
        mkdir($dir, 0775, true);
        @chmod($dir, 0775);  // let @, if www-data is not owner but allowed to write
      }
    }
    */
  }
  /**
   * Produire les exports depuis le fichier XML
   */
  public function export($srcfile, $setcode=null, $force=false) {
    $srcname = pathinfo($srcfile, PATHINFO_FILENAME);
    $srcmtime = filemtime($srcfile);
    $this->_sqlmtime->execute(array($srcname));
    list($basemtime) = $this->_sqlmtime->fetch();
    $teinte = null;
    if ($basemtime < $srcmtime) {
      $teinte = new Teinte_Doc($srcfile);
      $this->insert($teinte, $setcode);
    }
    $echo = "";
    foreach (self::$formats as $format => $extension) {
      $destfile = dirname(__FILE__).'/'.$format.'/'.$srcname.$extension;
      if (!$force && file_exists($destfile) && $srcmtime < filemtime($destfile)) continue;
      if (!$teinte) $teinte = new Teinte_Doc($srcfile);
      // delete destfile if exists ?
      if (file_exists($destfile)) unlink($destfile);
      $echo .= " ".$format;
      // TODO git $destfile
      if ($format == 'html') $teinte->html($destfile, 'http://oeuvres.github.io/Teinte/');
      if ($format == 'article') $teinte->article($destfile);
      else if ($format == 'markdown') $teinte->markdown($destfile);
      else if ($format == 'iramuteq') $teinte->iramuteq($destfile);
      else if ($format == 'epub') {
        $livre = new Livrable_Tei2epub($srcfile, self::$_logger);
        $livre->epub($destfile);
        // transformation auto en kindle si binaire présent
        if (self::$kindlegen) {
          $cmd = self::$kindlegen.' '.$destfile;
          $last = exec ($cmd, $output, $status);
          $mobi = dirname(__FILE__).'/'.$format.'/'.$teinte->filename.".mobi";
          // error ?
          if (!file_exists($mobi)) {
            self::log(E_USER_ERROR, "\n".$status."\n".join("\n", $output)."\n".$last."\n");
          }
          else {
            rename( $mobi, dirname(__FILE__).'/kindle/'.$teinte->filename.".mobi");
            $echo .= " kindle";
          }
        }
      }
      else if ($format == 'docx') {
        $echo .= " docx";
        Toff_Tei2docx::docx($srcfile, $destfile);
      }
    }
    if ($echo) self::log(E_USER_NOTICE, $srcfile.$echo);
  }
  /**
   * Insertion de la pièce
   */
  private function insert($srcfile, $setcode=null) {
    $teinte = new Teinte_Doc($srcfile);
    // supprimer la pièce, des triggers doivent normalement supprimer la cascade.
    $this->pdo->exec("DELETE FROM oeuvre WHERE code = ".$this->pdo->quote($teinte->filename));
    // métadonnées de pièces
    $year = null;
    $verse = null;
    $genrecode = null;
    $genre = null;
    $author = $teinte->xpath->query("/*/tei:teiHeader//tei:author");
    if ($author->length) $author = $author->item(0)->textContent;
    else $author = null;
    $nl = $teinte->xpath->query("/*/tei:teiHeader/tei:profileDesc/tei:creation/tei:date");
    if ($nl->length) {
      $n = $nl->item(0);
      $year = 0 + $n->getAttribute ('when');
      if(!$year) $year = 0 + $n->nodeValue;
    }
    if(!$year) $year = null;
    $title = $teinte->xpath->query("/*/tei:teiHeader//tei:title");
    if ($title->length) $title = $title->item(0)->textContent;
    else $title = null;

    $publisher = null;
    $identifier = null;
    $source = null;

    if (isset(self::$sets)) {
      if(isset(self::$sets[$setcode]['publisher'])) $publisher = self::$sets[$setcode]['publisher'];
      if(isset(self::$sets[$setcode]['identifier'])) $identifier = sprintf (self::$sets[$setcode]['identifier'], $teinte->filename);
      if (isset(self::$sets[$setcode]['source'])) $source = sprintf (self::$sets[$setcode]['source'], $teinte->filename);
    }

    $this->_insert->execute(array(
      $teinte->filename,
      $teinte->filemtime,
      $publisher,
      $identifier,
      $source,
      $author,
      $title,
      $year,

    ));
    return $teinte;
  }

  /**
   * Sortir le catalogue en table html
   */
  public function table($cols=array("no", "publisher", "creator", "date", "title", "downloads")) {
    $labels = array(
      "no"=>"N°",
      "publisher" => "Éditeur",
      "creator" => "Auteur",
      "date" => "Date",
      "title" => "Titre",
      "tei" => "Titre",
      "downloads" => "Téléchargements",
      "relation" => "Téléchargements",
    );
    echo '<table class="sortable">'."\n  <tr>\n";
    foreach ($cols as $code) {
      echo '    <th>'.$labels[$code]."</th>\n";
    }
    echo "  </tr>\n";
    $i = 1;
    foreach ($this->pdo->query("SELECT * FROM oeuvre ORDER BY code") as $oeuvre) {
      echo "  <tr>\n";
      foreach ($cols as $code) {
        if (!isset($labels[$code])) continue;
        echo "    <td>";
        if ("no" == $code) {
          echo $i;
        }
        else if ("publisher" == $code) {
          if ($oeuvre['identifier']) echo '<a href="'.$oeuvre['identifier'].'">'.$oeuvre['publisher']."</a>";
          else echo $oeuvre['publisher'];
        }
        else if("creator" == $code || "author" == $code) {
          echo $oeuvre['author'];
        }
        else if("date" == $code || "year" == $code) {
          echo $oeuvre['year'];
        }
        else if("title" == $code) {
          if ($oeuvre['identifier']) echo '<a href="'.$oeuvre['identifier'].'">'.$oeuvre['title']."</a>";
          else echo $oeuvre['title'];
        }
        else if("tei" == $code) {
          echo '<a href="'.$oeuvre['code'].'.xml">'.$oeuvre['title'].'</a>';
        }
        else if("relation" == $code || "downloads" == $code) {
          if ($oeuvre['source']) echo '<a href="'.$oeuvre['source'].'">TEI</a>';
          $sep = ", ";
          foreach ( self::$formats as $label=>$extension) {
            if ($label == 'article') continue;
            echo $sep.'<a href="'.$label.'/'.$oeuvre['code'].$extension.'">'.$label.'</a>';
          }
        }
        echo "</td>\n";
      }
      echo "  </tr>\n";
      $i++;
    }
    echo "\n</table>\n";
  }

  /**
   * Connexion à la base
   */
  private function connect($sqlite) {
    $dsn = "sqlite:" . $sqlite;
    // si la base n’existe pas, la créer
    if (!file_exists($sqlite)) {
      if (!file_exists($dir = dirname($sqlite))) {
        mkdir($dir, 0775, true);
        @chmod($dir, 0775);  // let @, if www-data is not owner but allowed to write
      }
      $this->pdo = new PDO($dsn);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
      @chmod($sqlite, 0775);
      $this->pdo->exec(self::$create);
    }
    else {
      $this->pdo = new PDO($dsn);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }
    // table temporaire en mémoire
    $this->pdo->exec("PRAGMA temp_store = 2;");
    $this->_insert = $this->pdo->prepare("
    INSERT INTO oeuvre (code, filemtime, publisher, identifier, source, author, title, year)
                VALUES (?,    ?,         ?,         ?,          ?,      ?,      ?,     ?);
    ");
    $this->_sqlmtime = $this->pdo->prepare("SELECT filemtime FROM oeuvre WHERE code = ?");
  }
  static function deps() {
    if(self::$_deps) return;
    // Deps
    $inc = dirname(__FILE__).'/../Livrable/Tei2epub.php';
    if (!file_exists($inc)) {
      echo "Impossible de trouver ".realpath(dirname(__FILE__).'/../')."/Livrable/
    Vous pouvez le télécharger sur https://github.com/oeuvres/Livrable\n";
      exit();
    }
    else {
      include_once($inc);
    }
    $inc = dirname(__FILE__).'/../Livrable/kindlegen';
    if (!file_exists($inc)) $inc = dirname(__FILE__).'/../Livrable/kindlegen.exe';
    if (!file_exists($inc)) {
      echo "Si vous souhaitez la conversion de vos epubs pour Kindle, il faut télécharger chez Amazon
      le programme kindlegen pour votre système http://www.amazon.fr/gp/feature.html?ie=UTF8&docId=1000570853
      à placer dans le dossier Livrable : ".dirname($inc);
    }
    if (file_exists($inc)) self::$kindlegen = realpath($inc);


    $inc = dirname(__FILE__).'/../Teinte/Doc.php';
    if (!file_exists($inc)) {
      echo "Impossible de trouver ".realpath(dirname(__FILE__).'/../')."/Teinte/
    Vous pouvez le télécharger sur https://github.com/oeuvres/Teinte\n";
      exit();
    }
    else {
      include_once($inc);
    }
    /*
    $inc = dirname(__FILE__).'/../Toff/Tei2docx.php';
    if (!file_exists($inc)) {
      echo "Impossible de trouver ".realpath(dirname(__FILE__).'/../')."/Toff/
    Vous pouvez le télécharger sur https://github.com/oeuvres/Toff\n";
      exit();
    }
    else {
      include_once($inc);
    }
    */
    self::$_deps=true;
  }
  /**
   * Custom error handler
   * May be used for xsl:message coming from transform()
   * To avoid Apache time limit, php could output some bytes during long transformations
   */
  static function log( $errno, $errstr=null, $errfile=null, $errline=null, $errcontext=null) {
    $errstr=preg_replace("/XSLTProcessor::transform[^:]*:/", "", $errstr, -1, $count);
    if ($count) { // is an XSLT error or an XSLT message, reformat here
      if(strpos($errstr, 'error')!== false) return false;
      else if ($errno == E_WARNING) $errno = E_USER_WARNING;
    }
    // not a user message, let work default handler
    else if ($errno != E_USER_ERROR && $errno != E_USER_WARNING && $errno != E_USER_NOTICE ) return false;
    // a debug message in normal mode, do nothing
    if ($errno == E_USER_NOTICE && !self::$debug) return true;
    if (!self::$_logger);
    else if (is_resource(self::$_logger)) fwrite(self::$_logger, $errstr."\n");
    else if ( is_string(self::$_logger) && function_exists(self::$_logger)) call_user_func(self::$_logger, $errstr);
  }
  static function epubcheck($glob) {
    echo "epubcheck epub/*.epub\n";
    foreach(glob($glob) as $file) {
      echo $file;
      // validation
      $cmd = "java -jar ".dirname(__FILE__)."/epubcheck/epubcheck.jar ".$file;
      $last = exec ($cmd, $output, $status);
      echo ' '.$status."\n";
      if ($status) rename($file, dirname($file).'/_'.basename($file));
    }
  }
  /**
   * Command line API
   */
  static function cli() {
    $timeStart = microtime(true);
    $usage = "\n usage    : php -f ".basename(__FILE__).' base.sqlite action "dir/*.xml"'."\n";
    array_shift($_SERVER['argv']); // shift first arg, the script filepath
    $sqlite = array_shift($_SERVER['argv']);
    $action = array_shift($_SERVER['argv']);
    $base = new Teinte_Build($sqlite, STDERR);
    while($glob = array_shift($_SERVER['argv']) ) {
      foreach(glob($glob) as $srcfile) {
        fwrite (STDERR, $srcfile);
        if ("insert" == $action) {
          $base->insert($srcfile);
        }

        // $base->add($file, $setcode);
        fwrite(STDERR, "\n");
      }
    }
    if ("insert" == $action) $base->table(array("no", "creator", "date", "tei"));
    /*
    array_shift($_SERVER['argv']); // shift first arg, the script filepath
    $sqlite = 'oeuvres.sqlite';
    // pas d’argument, on démarre sur les valeurs par défaut
    if (!count($_SERVER['argv'])) {
      $base = new Oeuvres($sqlite, STDERR);
      foreach(self::$sets as $setcode=>$setrow) {
        $glob = $setrow['glob'];
        foreach(glob($glob) as $file) {
          $base->add($file, $setcode);
        }
      }
      exit();
    }
    if ($_SERVER['argv'][0] == 'epubcheck') {
      Oeuvres::epubcheck('epub/*.epub');
      exit();
    }
    // des arguments, on joue plus fin
    $base = new Oeuvres($sqlite,  STDERR);
    if (!count($_SERVER['argv'])) exit("\n    Quel set insérer ?\n");
    $setcode = array_shift($_SERVER['argv']);
    foreach(glob(self::$sets[$setcode]['glob']) as $file) {
      $base->add($file, $setcode);
    }
    */

  }
}
?>
