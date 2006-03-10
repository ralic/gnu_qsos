<xsl:stylesheet version="1.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns="http://www.w3.org/TR/xhtml1/strict"> 
  <xsl:template match="/"> 
    <html> 
      <head> 
        <title><xsl:value-of select="document/header/appname" /><xsl:value-of select="document/header/release" /></title>
        <link rel="stylesheet" type="text/css" href="http://www.qsos.org/style/qsos-sheet.css" />
      </head> 
      <body> 
        <h1><xsl:value-of select="document/header/appname" /><xsl:value-of select="document/header/release" /></h1>
        <xsl:apply-templates select="document" />
        <small><a href="QSOS.org">the QSOS method</a></small>
      </body> 
    </html> 
  </xsl:template> 

  <xsl:template match="document">
    <div id="header">
      <xsl:apply-templates select="header" />
    </div>
    <xsl:apply-templates select="section" />
  </xsl:template>

  <xsl:template match="header">
    <ul>
      <xsl:apply-templates select="authors" />
      <xsl:apply-templates select="dates" />
      <li>Language: <xsl:value-of select="language" /></li>
      <li>Application: <xsl:value-of select="appli" /></li>
      <li>Release: <xsl:value-of select="release" /></li>
      <li>Url: <a href="{url}"><xsl:value-of select="url" /></a></li>
      <li>Desc: <xsl:value-of select="desc" /></li>
      <li>Demo: <xsl:value-of select="demo" /></li>
    </ul>
  </xsl:template>

  <xsl:template match="authors">
    <li>Authors:
      <ul>
        <xsl:apply-templates select="author" />
      </ul>
    </li>
  </xsl:template>
  <xsl:template match="author">
    <li><a href="mailto:{email}"><xsl:apply-templates select="name" /> </a></li>
  </xsl:template>

  <xsl:template match="dates">
    <li>Creation: <xsl:apply-templates select="creation" /></li>
    <li>Validation: <xsl:apply-templates select="validation" /></li>
  </xsl:template>

  <xsl:template match="section">
    <div class="section" id="{@name}">
      <h2><xsl:value-of select="@title" /></h2>
      <xsl:value-of select="desc" />
      <ul>
        <xsl:apply-templates select="element" />
      </ul>
    </div>
  </xsl:template>

  <xsl:template match="element">
    <li>
      <div class="element" id="{@name}">
        <strong><xsl:value-of select="@title" /></strong>
        <xsl:if test="desc">
        <p>
          <xsl:value-of select="desc" />
        </p>
      </xsl:if>
      <xsl:if test="score">
        <div class="score">Score : <xsl:value-of select="score" />/3</div>
      </xsl:if>
      </div>
        <xsl:if test="element">
          <ul>
            <xsl:apply-templates select="element" />
          </ul>
        </xsl:if>
    </li>
  </xsl:template>





</xsl:stylesheet>
