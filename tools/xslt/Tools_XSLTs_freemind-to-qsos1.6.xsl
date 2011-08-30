<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml" indent="yes" encoding="UTF-8"/>

  <xsl:template match="map">
    <xsl:element name="document">
      <xsl:apply-templates select="node"/>
    </xsl:element>
  </xsl:template>

  <xsl:template match="node">
    <xsl:choose>
      <xsl:when test="parent::map">
	<header>
	    <authors>
	      <author>
		  <email></email>
		  <name></name>
	      </author>
	    </authors>
	    <dates>
	      <creation></creation>
	      <validation></validation>
	    </dates>
	    <appname></appname>
	    <desc></desc>
	    <release></release>
	    <licenseid></licenseid>
	    <licensedesc></licensedesc>
	    <url></url>
	    <demourl></demourl>
	    <language>fr</language>
	    <qsosappname></qsosappname>
	    <qsosformat>1.0</qsosformat>
	    <qsosspecificformat></qsosspecificformat>
	    <qsosappfamily><xsl:value-of select="@TEXT"/></qsosappfamily>
	</header>
	<xsl:apply-templates select="node"/>
      </xsl:when>
      <xsl:when test="./@STYLE='bubble'">
      	<desc><xsl:value-of select="@TEXT"/></desc>
      </xsl:when>
      <xsl:when test="child::icon">
      	<xsl:if test="icon/@BUILTIN = 'full-0'"><desc0><xsl:value-of select="@TEXT"/></desc0></xsl:if>
      	<xsl:if test="icon/@BUILTIN = 'full-1'"><desc1><xsl:value-of select="@TEXT"/></desc1></xsl:if>
      	<xsl:if test="icon/@BUILTIN = 'full-2'"><desc2><xsl:value-of select="@TEXT"/></desc2></xsl:if>
      </xsl:when>
      <xsl:when test="count(ancestor::node()) = 3">
        <section name="{@ID}" title="{@TEXT}">
          <xsl:apply-templates select="attribute"/>
	  <xsl:apply-templates select="node"/>
         </section>
      </xsl:when>
      <xsl:otherwise>
        <element name="{@ID}" title="{@TEXT}">
	  <xsl:apply-templates select="attribute"/>
	  <xsl:apply-templates select="node"/>
	  <xsl:if test="child::node/icon">
	  	<comment></comment>
	  	<score></score>
	  	</xsl:if>
        </element>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template match="attribute">
    <xsl:element name="{@NAME}">
      <xsl:value-of select="@VALUE"/>
    </xsl:element>
  </xsl:template>
  
</xsl:stylesheet>
