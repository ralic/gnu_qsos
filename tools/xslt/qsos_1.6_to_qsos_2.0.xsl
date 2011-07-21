<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml" indent="yes" encoding="UTF-8"/>

  <xsl:template match="document">
    <xsl:element name="document">
      <xsl:apply-templates select="header"/>
      <xsl:apply-templates select="section"/>
    </xsl:element>
  </xsl:template>

  <xsl:template match="header">
    <xsl:element name="header">
      <xsl:element name="metadata">
	<xsl:apply-templates select="authors"/>
	<xsl:apply-templates select="dates"/>
	<xsl:apply-templates select="language"/>
	<xsl:apply-templates select="qsosformat"/>
	<xsl:apply-templates select="qsosspecificformat"/>
      </xsl:element>
      <xsl:element name="cartouche">
	<xsl:attribute name="version">0.1</xsl:attribute>
	<Component>
	  <xsl:apply-templates select="appname"/>
	  <xsl:apply-templates select="release"/>
	  <xsl:apply-templates select="url"/>
	  <status/>
	  <releaseDate/>
	  <xsl:apply-templates select="qsosappfamily"/>
	  <mainTech/>
	</Component>
	<license>
	  <xsl:apply-templates select="licensedesc"/>
	  <version></version>
	  <homepage></homepage>
	</license>
	<team/>
	<legal/>
	<misc/>
      </xsl:element>
    </xsl:element>
  </xsl:template>

  <xsl:template match="appname">
    <xsl:element name="name"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="release">
    <xsl:element name="version"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="url">
    <xsl:element name="homepage"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="qsosappfamily">
    <xsl:element name="type"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="licensedesc">
    <xsl:element name="name"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="authors">
    <authors>
      <xsl:apply-templates select="author"/>
    </authors>
  </xsl:template>

  <xsl:template match="author">
    <author>
      <name><xsl:value-of select="name"/></name>
      <email><xsl:value-of select="email"/></email>
    </author>
  </xsl:template>

  <xsl:template match="dates">
    <dates>
      <creation><xsl:value-of select="creation"/></creation>
      <validation><xsl:value-of select="validation"/></validation>
      <update/>
    </dates>
  </xsl:template>

  <xsl:template match="language">
    <xsl:element name="language"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="qsosformat">
    <xsl:element name="qsosversion">2.0</xsl:element>
  </xsl:template>

  <xsl:template match="qsosspecificformat">
    <xsl:element name="templateversion"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="@*|node()">
    <xsl:copy><xsl:apply-templates select="@*|node()"/></xsl:copy>
  </xsl:template>

</xsl:stylesheet>