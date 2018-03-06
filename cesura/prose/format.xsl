<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet exclude-result-prefixes="xs t" version="2.0" xmlns="http://www.tei-c.org/ns/1.0" xmlns:t="http://www.tei-c.org/ns/1.0" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output encoding="UTF-8" indent="yes"/>
  <xsl:template match="t:body//t:s/text()">
      <xsl:value-of select="normalize-space()"/>
  </xsl:template>
  <xsl:template match="* | @* | comment() | processing-instruction() | text()">
    <xsl:apply-templates></xsl:apply-templates>
  </xsl:template>  
</xsl:stylesheet>
