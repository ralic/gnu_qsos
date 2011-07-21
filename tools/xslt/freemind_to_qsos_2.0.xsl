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
    <header>
      <qsosMetadata>
         <template>
            <authors/>
            <reviewer>
               <name/>
               <email/>
               <date/>
               <comment/>
            </reviewer>
            <dates>
               <creation/>
               <update/>
               <validation/>
            </dates>
         </template>
         <evaluation>
            <authors/>
            <reviewer>
               <name/>
               <email/>
               <date/>
               <comment/>
            </reviewer>
            <dates>
               <creation/>
               <update/>
               <validation/>
            </dates>
         </evaluation>
         <language/>
         <version/>
      </qsosMetadata>
      <openSourceCartouche>
         <metadata>
            <version>0.2 Beta</version>
            <author>
               <name/>
               <email/>
               <comment/>
            </author>
            <reviewer>
               <name/>
               <email/>
               <date/>
               <comment/>
            </reviewer>
            <dates>
               <creation/>
               <update/>
               <validation/>
            </dates>
         </metadata>
         <component>
            <name/>
            <version/>
            <description/>
            <archetype/>
            <vendor/>
            <homepage/>
            <status/>
            <releaseDate/>
            <type/>
            <mainTech/>
         </component>
         <license>
            <name/>
            <version/>
            <homepage/>
         </license>
         <team>
            <number/>
            <developers/>
            <contributors/>
         </team>
         <legal>
            <copyright/>
            <patent>
               <name/>
               <publicationDate/>
               <description/>
            </patent>
            <cypher>
               <name/>
               <exportRestrictions/>
            </cypher>
         </legal>
         <misc>
            <comment/>
            <fileNumber/>
            <data>
               <volume/>
               <unit/>
            </data>
         </misc>
      </openSourceCartouche>
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
