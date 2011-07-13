<?xml version="1.0"?>
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
      <xsl:element name="Cartouche">
	<xsl:attribute name="Version">0.1</xsl:attribute>
	<Component>
	  <xsl:apply-templates select="appname"/>
	  <xsl:apply-templates select="release"/>
	  <xsl:apply-templates select="url"/>
	  <Status/>
	  <ReleaseDate/>
	  <xsl:apply-templates select="qsosappfamily"/>
	  <MainTech/>
	</Component>
	<License>
	  <xsl:apply-templates select="licensedesc"/>
	  <LicenseVersion></LicenseVersion>
	  <LicenseHomepage></LicenseHomepage>
	</License>
	<Team/>
	<Legal/>
	<Misc/>
      </xsl:element>  
    </xsl:element>  
  </xsl:template>  

  <xsl:template match="appname">  
    <xsl:element name="ComponentName"><xsl:apply-templates select="@*|node()"/></xsl:element>  
  </xsl:template> 

  <xsl:template match="release">  
    <xsl:element name="ComponentVersion"><xsl:apply-templates select="@*|node()"/></xsl:element>  
  </xsl:template>

  <xsl:template match="url">  
    <xsl:element name="ComponentHomepage"><xsl:apply-templates select="@*|node()"/></xsl:element>  
  </xsl:template>

  <xsl:template match="qsosappfamily">  
    <xsl:element name="Type"><xsl:apply-templates select="@*|node()"/></xsl:element>  
  </xsl:template>

  <xsl:template match="licensedesc">  
    <xsl:element name="LicenseName"><xsl:apply-templates select="@*|node()"/></xsl:element>  
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