<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml" indent="yes" encoding="UTF-8"/>

  <xsl:template match="document">
      <xsl:element name="map">
        <xsl:attribute name="version">0.7.1</xsl:attribute>
	      <xsl:element name="node">
	        <xsl:attribute name="ID"><xsl:value-of select="header/qsosappfamily"/></xsl:attribute>
	        <xsl:attribute name="TEXT"><xsl:value-of select="header/qsosappfamily"/></xsl:attribute>
	        <font NAME="SansSerif" BOLD="true" SIZE="12"/>
	        <xsl:apply-templates select="section"/>
	      </xsl:element>
      </xsl:element>
  </xsl:template>

  <xsl:template match="section">
    <node ID="{@name}" TEXT="{@title}">
      <font NAME="SansSerif" BOLD="true" SIZE="12"/>
      <xsl:element name="node">
        <xsl:attribute name="TEXT"><xsl:value-of select="desc"/>
        </xsl:attribute><xsl:attribute name="STYLE">bubble</xsl:attribute>
	    <font NAME="SansSerif" ITALIC="true" SIZE="10"/>
      </xsl:element>
      <xsl:apply-templates select="element"/>
    </node>
  </xsl:template>

  <xsl:template match="element">
    <node ID="{@name}" TEXT="{@title}">
    <xsl:if test="desc != ''">
	    <xsl:attribute name="FOLDED">true</xsl:attribute>
	    <xsl:element name="node">
	      <xsl:attribute name="TEXT"><xsl:value-of select="desc"/></xsl:attribute>
          <xsl:attribute name="STYLE">bubble</xsl:attribute>
	      <font NAME="SansSerif" ITALIC="true" SIZE="10"/>
	    </xsl:element>
    </xsl:if>
    <xsl:if test="desc0 != ''">
      <xsl:element name="node">
        <xsl:attribute name="TEXT"><xsl:value-of select="desc0"/></xsl:attribute>
        <icon BUILTIN="full-0"/>
      </xsl:element>
    </xsl:if>
    <xsl:if test="desc1 != ''">
      <xsl:element name="node">
        <xsl:attribute name="TEXT"><xsl:value-of select="desc1"/></xsl:attribute>
        <icon BUILTIN="full-1"/>
      </xsl:element>
    </xsl:if>
    <xsl:if test="desc2 != ''">
      <xsl:element name="node">
        <xsl:attribute name="TEXT"><xsl:value-of select="desc2"/></xsl:attribute>
        <icon BUILTIN="full-2"/>
      </xsl:element>
    </xsl:if>
    <xsl:apply-templates select="element"/>
    </node>
  </xsl:template>
</xsl:stylesheet>
