<?xml version="1.0" encoding="UTF-8"?>
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
      <qsosMetadata>
	  <template>
	    <xsl:apply-templates select="//node[@ID='authors']"/>
	    <reviewer>
		<name><xsl:value-of select="//node[@ID='reviewer_name_entry']/@TEXT"/></name>
		<email><xsl:value-of select="//node[@ID='reviewer_email_entry']/@TEXT"/></email>
		<reviewDate><xsl:value-of select="//node[@ID='review_date_entry']/@TEXT"/></reviewDate>
		<comment><xsl:value-of select="//node[@ID='reviewer_comment_entry']/@TEXT"/></comment>
	    </reviewer>
	    <dates>
		<creation><xsl:value-of select="//node[@ID='creation_entry']/@TEXT"/></creation>
		<update><xsl:value-of select="//node[@ID='update_entry']/@TEXT"/></update>
		<validation></validation>
	    </dates>
	    <version><xsl:value-of select="//node[@ID='version_entry']/@TEXT"/></version>
	    <type><xsl:value-of select="//node[@ID='type']/@TEXT"/></type>
	  </template>
	  <evaluation>
	    <authors>
		<author>
		  <name></name>
		  <email></email>
		  <comment></comment>
		</author>
	    </authors>
	    <reviewer>
		<name></name>
		<email></email>
		<reviewDate></reviewDate>
		<comment></comment>
	    </reviewer>
	    <dates>
		<creation></creation>
		<update></update>
		<validation></validation>
	    </dates>
	  </evaluation>
	  <language><xsl:value-of select="//node[@ID='language_entry']/@TEXT"/></language>
	  <qsosVersion>2.0</qsosVersion>
      </qsosMetadata>
      <openSourceCartouche>
	  <metadata>
	    <cartoucheVersion>0.2 Beta</cartoucheVersion>
	    <author>
		<name></name>
		<email></email>
		<comment></comment>
	    </author>
	    <reviewer>
		<name></name>
		<email></email>
		<reviewDate></reviewDate>
		<comment></comment>
	    </reviewer>
	    <dates>
		<creation></creation>
		<update></update>
		<validation></validation>
	    </dates>
	  </metadata>
	  <component>
	    <name></name>
	    <version></version>
	    <status></status>
	    <releaseDate></releaseDate>
	    <homepage></homepage>
	    <description></description>
	    <archetype></archetype>
	    <vendor></vendor>
	    <tags></tags>
	    <mainTech></mainTech>
	    <checksum></checksum>
	  </component>
	  <license>
	    <name></name>
	    <version></version>
	    <homepage></homepage>
	  </license>
	  <team>
	    <number></number>
	    <developers>
		<developer>
		  <name></name>
		  <email></email>
		  <company></company>
		</developer>
	    </developers>
	    <contributors>
		<contributor>
		  <name></name>
		  <email></email>
		  <company></company>
		</contributor>
	    </contributors>
	  </team>
	  <legal>
	    <copyright></copyright>
	    <patents>
	      <patent>
		<ipcNumber></ipcNumber>
		<name></name>
		<publicationDate></publicationDate>
		<description></description>
	      </patent>
	    </patents>
	    <cypher>
		<name></name>
		<exportRestrictions></exportRestrictions>
	    </cypher>
	  </legal>
	  <misc>
	    <comment></comment>
	    <fileNumber>1</fileNumber>
	    <data>
		<volume></volume>
		<unit></unit>
	    </data>
	    <dependencies></dependencies>
	  </misc>
      </openSourceCartouche>
      <xsl:apply-templates select="node"/>
    </xsl:when>
    <xsl:when test="./@STYLE='bubble'">
      <desc><xsl:value-of select="@TEXT"/></desc>
    </xsl:when>
    <xsl:when test="@ID = 'metadata'"></xsl:when>
    <xsl:when test="@ID = 'authors'">
      <authors>
	<xsl:apply-templates select="node"/>
      </authors>
    </xsl:when>
    <xsl:when test="@TEXT = 'author' and ancestor::node/@ID = 'authors'">
      <author>
	<xsl:apply-templates select="node"/>
      </author>
    </xsl:when>
    <xsl:when test="@TEXT = 'name' and ancestor::node/ancestor::node/@ID = 'authors'">
	<name><xsl:value-of select="child::node/@TEXT"/></name>
    </xsl:when>
    <xsl:when test="@TEXT = 'email' and ancestor::node/ancestor::node/@ID = 'authors'">
	<email><xsl:value-of select="child::node/@TEXT"/></email>
    </xsl:when>
    <xsl:when test="@TEXT = 'comment' and ancestor::node/ancestor::node/@ID = 'authors'">
	<comment><xsl:value-of select="child::node/@TEXT"/></comment>
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