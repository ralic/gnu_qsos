<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:str="http://exslt.org/strings" xmlns:fn="http://www.w3.org/2005/xpath-functions" version="1.0">
<xsl:output method="xml" indent="yes" encoding="UTF-8"/>

  <xsl:template match="document">
    <xsl:element name="document">
      <xsl:apply-templates select="header"/>
      <xsl:apply-templates select="section"/>
    </xsl:element>
  </xsl:template>

  <xsl:template match="header">
      <xsl:element name="qsosMetadata">
        <xsl:element name="template">
	  <xsl:apply-templates select="qsosappfamily"/>
	  <xsl:apply-templates select="qsosspecificformat"/>
        </xsl:element>
        <xsl:element name="evaluation">
	  <xsl:apply-templates select="authors"/>
	  <reviewer>
	    <name/>
	    <email/>
	    <reviewDate/>
	    <comment/>
	  </reviewer>
	  <xsl:apply-templates select="dates"/>
        </xsl:element>
	<xsl:apply-templates select="language"/>
	<xsl:apply-templates select="qsosformat"/>
      </xsl:element>
      <xsl:element name="openSourceCartouche">
	<metadata>
          <cartoucheVersion>1.0</cartoucheVersion>
          <author>
	    <name/>
	    <email/>
	    <comment/>
	  </author>
	  <reviewer>
	    <name/>
	    <email/>
	    <comment/>
	    <reviewDate/>
	  </reviewer>
	  <xsl:apply-templates select="dates"/>
	</metadata>
	<component>
	  <xsl:apply-templates select="appname"/>
	  <xsl:apply-templates select="release"/>
	  <xsl:apply-templates select="desc"/>
	  <archetype/>
	  <vendor/>
	  <xsl:apply-templates select="url"/>
	  <status/>
	  <releaseDate/>
	  <xsl:apply-templates select="qsosappfamily"/>
	  <tags/>
	  <mainTech/>
	</component>
	<license>
	  <xsl:apply-templates select="licensedesc"/>
	  <version></version>
	  <homepage></homepage>
	</license>
	<team>
	  <number/>
	</team>
	<legal>
	  <copyright/>
	</legal>
	<misc/>
      </xsl:element>
  </xsl:template>

  <xsl:template match="appname">
    <xsl:element name="name"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="release">
    <xsl:element name="version"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="desc">
    <xsl:element name="description"><xsl:apply-templates select="@*|node()"/></xsl:element>
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
      <creation>
	<xsl:choose>
	  <xsl:when test="contains(creation,'/')">
	    <xsl:variable name="tokenizedDate" select="str:tokenize(creation,'/')"/>
	    <xsl:value-of select="$tokenizedDate[3]"/>-<xsl:value-of select="$tokenizedDate[2]"/>-<xsl:value-of select="$tokenizedDate[1]"/>
	  </xsl:when>
	  <xsl:otherwise>
	    <xsl:value-of select="creation"/>
	  </xsl:otherwise>
        </xsl:choose>
      </creation>
      <validation>
	<xsl:choose>
	  <xsl:when test="contains(validation,'/')">
	    <xsl:variable name="tokenizedDate" select="str:tokenize(validation,'/')"/>
	    <xsl:value-of select="$tokenizedDate[3]"/>-<xsl:value-of select="$tokenizedDate[2]"/>-<xsl:value-of select="$tokenizedDate[1]"/>
	  </xsl:when>
	  <xsl:otherwise>
	    <xsl:value-of select="validation"/>
	  </xsl:otherwise>
        </xsl:choose>
      </validation>
      <update/>
    </dates>
  </xsl:template>

  <xsl:template match="language">
    <xsl:element name="language"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="qsosformat">
    <xsl:element name="qsosVersion">2.0</xsl:element>
  </xsl:template>

  <xsl:template match="qsosspecificformat">
    <xsl:element name="version"><xsl:apply-templates select="@*|node()"/></xsl:element>
  </xsl:template>

  <xsl:template match="@*|node()">
    <xsl:copy><xsl:apply-templates select="@*|node()"/></xsl:copy>
  </xsl:template>

</xsl:stylesheet>