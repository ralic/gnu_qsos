<?xml version="1.0"?>
<!-- 
TODO:
remove the <?xml version="1.0"?> from the created file
add '=' around the title for every subsection.
--!>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/TR/xhtml1/strict" version="1.0">
<xsl:template match="/">
=<xsl:value-of select="document/header/appname"/>&#160;<xsl:value-of
select="document/header/release"/>=
<xsl:apply-templates select="document"/>
[http://QSOS.org the QSOS method]
</xsl:template>
<xsl:template match="document">
==Information==
<xsl:apply-templates select="header"/>
<xsl:apply-templates select="section"/>
</xsl:template>
<xsl:template match="header">
<xsl:apply-templates select="dates"/>
Language: <xsl:value-of select="language"/>
Application: <xsl:value-of select="appname"/>
Release: <xsl:value-of select="release"/>
License: <xsl:value-of select="licensedesc"/>
Url: [<xsl:value-of select="url"/>]
Desc: <xsl:value-of select="desc"/>
<xsl:if test="demo != ''">
Demo: <xsl:value-of select="demo"/>
</xsl:if>
<xsl:apply-templates select="authors"/>
You can access to the sheet change log on [http://cvs.savannah.nongnu.org/viewcvs/qsos/sheet/?root=qsos the CVS]
</xsl:template>
<xsl:template match="authors">
Authors of this sheet: <xsl:apply-templates select="author"/>
</xsl:template>
<xsl:template match="author">
[mailto:<xsl:apply-templates select="email"/>&#160;<xsl:apply-templates
select="name"/>]
</xsl:template>
<xsl:template match="dates">
Sheet created on <xsl:apply-templates select="creation"/>
<xsl:if test="validation != ''">
Sheet validation the <xsl:apply-templates select="validation"/>
</xsl:if>
</xsl:template>
<xsl:template match="section">
== <xsl:value-of select="@title"/> ==
<xsl:value-of select="desc"/>
<xsl:apply-templates select="element"/>
</xsl:template>
<xsl:template match="element">
===  <xsl:value-of select="@title"/> ===
<xsl:if test="desc0">
<xsl:if test="score = '0'">
* '''<xsl:value-of select="desc0"/>'''
* <xsl:value-of select="desc1"/>
* <xsl:value-of select="desc2"/>
</xsl:if>
<xsl:if test="score = '1'">
* <xsl:value-of select="desc0"/>
* '''<xsl:value-of select="desc1"/>'''
* <xsl:value-of select="desc2"/>
</xsl:if>
<xsl:if test="score = '2'">
* <xsl:value-of select="desc0"/>
* <xsl:value-of select="desc1"/>
* '''<xsl:value-of select="desc2"/>'''
</xsl:if>
<xsl:if test="score = ''">
* <xsl:value-of select="desc0"/>
* <xsl:value-of select="desc1"/>
* <xsl:value-of select="desc2"/>
</xsl:if>
</xsl:if>
<xsl:if test="comment !=''">
Comment: <xsl:value-of select="comment"/>
</xsl:if>
<xsl:if test="score != ''">
Score: '''<xsl:value-of select="score"/>'''/2</xsl:if>
<xsl:if test="score = ''">
Score: Not evaluated
</xsl:if>
<xsl:if test="element">
<xsl:apply-templates select="element"/>
</xsl:if>
</xsl:template>
</xsl:stylesheet>
