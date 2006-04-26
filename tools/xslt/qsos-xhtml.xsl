<xsl:stylesheet version="1.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns="http://www.w3.org/TR/xhtml1/strict"> 
  <xsl:template match="/"> 
    <html> 
      <head> 
        <title><xsl:value-of select="document/header/appname" /><xsl:value-of select="document/header/release" /></title>
        <link rel="stylesheet" type="text/css" href="%%CSS_SHEET%%" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      </head> 
      <body> 
        <h1><xsl:value-of select="document/header/appname" /><xsl:value-of select="document/header/release" /></h1>
        <xsl:apply-templates select="document" />
        <small><a href="http://QSOS.org">the QSOS method</a></small>
      </body> 
    </html> 
  </xsl:template> 

  <xsl:template match="document">
    <div id="header">
    <h2>Information</h2>
      <xsl:apply-templates select="header" />
    </div>
    <xsl:apply-templates select="section" />
  </xsl:template>

  <xsl:template match="header">
    <ul>
      <xsl:apply-templates select="dates" />
      <li><strong>Language: </strong> <xsl:value-of select="language" /></li>
      <li><strong>Application: </strong> <xsl:value-of select="appname" /></li>
      <li><strong>Release: </strong> <xsl:value-of select="release" /></li>
      <li><strong>License: </strong> <xsl:value-of select="licensedesc" /></li>
      <li><strong>Url: </strong> <a href="{url}"><xsl:value-of select="url" /></a></li>
      <li><strong>Desc: </strong> <xsl:value-of select="desc" /></li>

      <xsl:if test="demo != ''">
      <li><strong>Demo: </strong> <xsl:value-of select="demo" /></li>
      </xsl:if>

      <xsl:apply-templates select="authors" />
      <p>
        You can access to the sheet change log on <a href="http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos">the CVS</a>.
      </p>
    </ul>
  </xsl:template>

  <xsl:template match="authors">
    <li><strong>Authors of this sheet: </strong> <xsl:apply-templates select="author" />
    </li>
  </xsl:template>
  <xsl:template match="author">
    <a href="mailto:{email}"><xsl:apply-templates select="name" /> </a>  
  </xsl:template>

  <xsl:template match="dates">
    <li><strong>Sheet created the </strong> <xsl:apply-templates select="creation" /></li>

    <xsl:if test="validation != ''">
    <li><strong>Sheet validation the </strong> <xsl:apply-templates select="validation" /></li>
    </xsl:if>

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
	
	<xsl:if test="desc0">
	<ul type="desclist">
		 <xsl:if test="score = '0'">	
			<li> <strong><xsl:value-of select="desc0" /></strong>  </li>
			<li> <xsl:value-of select="desc1" /> </li>
			<li> <xsl:value-of select="desc2" /> </li>
       		</xsl:if>
		<xsl:if test="score = '1'">	
			<li> <xsl:value-of select="desc0" /> </li>
			<li> <strong><xsl:value-of select="desc1" /></strong>  </li>
			<li> <xsl:value-of select="desc2" /> </li>
		</xsl:if>
		<xsl:if test="score = '2'">	
			<li> <xsl:value-of select="desc0" /> </li>
			<li> <xsl:value-of select="desc1" /> </li>
			<li> <strong><xsl:value-of select="desc2" /></strong>  </li>
		</xsl:if>
		<xsl:if test="score = ''">	
			<li> <xsl:value-of select="desc0" /> </li>
			<li> <xsl:value-of select="desc1" /> </li>
			<li> <xsl:value-of select="desc2" /> </li>
		</xsl:if>
			
	</ul>
	</xsl:if>	
	
	
	<xsl:if test="comment !=''">
		<ul>
			<div class="comments"><xsl:value-of select="comment"/></div>
		</ul>
	</xsl:if>	
	
	
	
	<xsl:if test="score != ''">
		<div class="score">Score : <xsl:value-of select="score" />/2</div>
	
	</xsl:if>
	<xsl:if test="score = ''">
		<div class="todo">Not evaluated</div> 
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
