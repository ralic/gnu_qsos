<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method="xml" indent="yes" encoding="UTF-8" omit-xml-declaration="yes"/>

  <xsl:template match="document">
      <xsl:element name="map">
        <xsl:attribute name="version">0.9.0</xsl:attribute>
	<xsl:element name="node">
	  <xsl:attribute name="ID">type</xsl:attribute>
	  <xsl:attribute name="TEXT"><xsl:value-of select="qsosMetadata/template/type"/></xsl:attribute>
	  <xsl:apply-templates select="section"/>
	  <node CREATED="1311176248788" ID="metadata" MODIFIED="1311176284873" POSITION="right" TEXT="Metadata">
	    <font NAME="SansSerif" BOLD="true" SIZE="12"/>
	    <node BACKGROUND_COLOR="#ffcccc" CREATED="1311176012521" ID="ID_424723663" MODIFIED="1311234530138" STYLE="bubble">
	      <richcontent TYPE="NODE"><html><head></head>
		  <body>
		      <xsl:choose>
			<xsl:when test="qsosMetadata/language = 'FR'">
			  <p>
			    <i>Cette zone est r&#xE9;serv&#xE9;e aux meta donn&#xE9;es relatives au template. </i>
			  </p>
			  <p><i>Merci de la compl&#xE9;ter si n&#xE9;cessaire, notamment si vous en modifier la structure et le contenu des autres axes, pensez alors &#xE0; mettre &#xE0; jour la version, la date de mise &#xE0; jour (</i>update<i>) et les auteurs (</i>authors<i>).</i></p>
			</xsl:when>
			<xsl:otherwise>
			  <p>
			    <i>This zone is dedicated to template metadata. </i>
			  </p>
			  <p><i>Please fill when you modify the template's structure or contents and do not forget to update here the followwing metadata: version, updtae date, authors (if you'r a new author, add a new entry).</i></p>
			</xsl:otherwise>
		      </xsl:choose>
		  </body>
		</html>
	      </richcontent>
	      <icon BUILTIN="messagebox_warning"/>
	    </node>
	    <node CREATED="1311176592584" ID="version" MODIFIED="1311177836867" TEXT="version">
	      <node CREATED="1311176605382" ID="version_entry" MODIFIED="1311234866394" STYLE="bubble">
		<xsl:attribute name="TEXT"><xsl:value-of select="qsosMetadata/template/version"/></xsl:attribute>
		<font ITALIC="true" NAME="SansSerif" SIZE="12"/>
	      </node>
	    </node>
	    <node CREATED="1311177840941" ID="language" MODIFIED="1311177848879" TEXT="language">
	      <node CREATED="1311176605382" ID="language_entry" MODIFIED="1311234870295" STYLE="bubble">
		<xsl:attribute name="TEXT"><xsl:value-of select="translate(qsosMetadata/language, 'abcdefghijklmnopqrstuvwxyz', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ')"/></xsl:attribute>
		<font ITALIC="true" NAME="SansSerif" SIZE="12"/>
	      </node>
	    </node>
	    <node CREATED="1311176311468" ID="authors" MODIFIED="1311176319000" TEXT="authors">
	      <xsl:apply-templates select="qsosMetadata/template/authors/author"/>
	    </node>
	    <node CREATED="1311176321071" ID="reviewer" MODIFIED="1311176511407" TEXT="reviewer">
	      <node CREATED="1311176326649" ID="reviewer_name" MODIFIED="1311176329456" TEXT="name">
		<node CREATED="1311176352743" ID="reviewer_name_entry" MODIFIED="1311234911615" STYLE="bubble">
		  <xsl:attribute name="TEXT"><xsl:value-of select="qsosMetadata/template/reviewer/name"/></xsl:attribute>
		  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
		</node>
	      </node>
	      <node CREATED="1311176333060" ID="reviewer_email" MODIFIED="1311176335780" TEXT="email">
		<node CREATED="1311176383595" ID="reviewer_email_entry" MODIFIED="1311234898697" STYLE="bubble">
		  <xsl:attribute name="TEXT"><xsl:value-of select="qsosMetadata/template/reviewer/email"/></xsl:attribute>
		  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
		</node>
	      </node>
	      <node CREATED="1311176348053" ID="reviewer_comment" MODIFIED="1311176350924" TEXT="comment">
		<node CREATED="1311176394385" ID="reviewer_comment_entry" MODIFIED="1311234903352" STYLE="bubble">
		  <xsl:attribute name="TEXT"><xsl:value-of select="qsosMetadata/template/reviewer/comment"/></xsl:attribute>
		  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
		</node>
	      </node>
	      <node CREATED="1311176549844" ID="review_date" MODIFIED="1311176554478" TEXT="reviewDate">
		<node CREATED="1311176394385" ID="review_date_entry" MODIFIED="1311234907914" STYLE="bubble">
		  <xsl:attribute name="TEXT"><xsl:value-of select="qsosMetadata/template/reviewer/reviewDate"/></xsl:attribute>
		  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
		</node>
	      </node>
	    </node>
	    <node CREATED="1311176675856" ID="dates" MODIFIED="1311176680299" TEXT="dates">
	      <node CREATED="1311176682141" ID="creation" MODIFIED="1311176685329" TEXT="creation">
		<node CREATED="1311176696701" ID="creation_entry" MODIFIED="1311234932081" STYLE="bubble">
		  <xsl:attribute name="TEXT"><xsl:value-of select="qsosMetadata/template/dates/creation"/></xsl:attribute>
		  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
		</node>
	      </node>
	      <node CREATED="1311176691625" ID="update" MODIFIED="1311176695012" TEXT="update">
		<node CREATED="1311176696701" ID="update_entry" MODIFIED="1311234935855" STYLE="bubble">
		  <xsl:attribute name="TEXT"><xsl:value-of select="qsosMetadata/template/dates/creation"/></xsl:attribute>
		  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
		</node>
	      </node>
	    </node>
	  </node>
	</xsl:element>
      </xsl:element>
  </xsl:template>

  <xsl:template match="section">
    <node ID="{@name}" TEXT="{@title}">
      <xsl:if test="position() mod 2 = 0">
	<xsl:attribute name="POSITION">left</xsl:attribute>
      </xsl:if>
      <xsl:if test="position() mod 2 = 1">
	<xsl:attribute name="POSITION">right</xsl:attribute>
      </xsl:if>
      <font NAME="SansSerif" BOLD="true" SIZE="12"/>
      <xsl:element name="node">
	<xsl:attribute name="TEXT"><xsl:value-of select="desc"/></xsl:attribute>
	<xsl:attribute name="STYLE">bubble</xsl:attribute>
	<font NAME="SansSerif" ITALIC="true" SIZE="10"/>
      </xsl:element>
      <xsl:apply-templates select="element"/>
    </node>
  </xsl:template>

  <xsl:template match="element">
    <xsl:element name="node">
      <xsl:attribute name="ID"><xsl:value-of select="@name"/></xsl:attribute>
      <xsl:attribute name="TEXT"><xsl:value-of select="@title"/></xsl:attribute>

      <xsl:element name="node">
	<xsl:attribute name="TEXT"><xsl:value-of select="desc"/></xsl:attribute>
	<xsl:attribute name="STYLE">bubble</xsl:attribute>
	<font NAME="SansSerif" ITALIC="true" SIZE="10"/>
      </xsl:element>

      <xsl:if test = 'desc0'>
	<xsl:element name="node">
	  <xsl:attribute name="TEXT"><xsl:value-of select="desc0"/></xsl:attribute>
	  <icon BUILTIN="full-0"/>
	</xsl:element>
	<xsl:element name="node">
	  <xsl:attribute name="TEXT"><xsl:value-of select="desc1"/></xsl:attribute>
	  <icon BUILTIN="full-1"/>
	</xsl:element>
	<xsl:element name="node">
	  <xsl:attribute name="TEXT"><xsl:value-of select="desc2"/></xsl:attribute>
	  <icon BUILTIN="full-2"/>
	</xsl:element>
      </xsl:if>

      <xsl:apply-templates select="element"/>

    </xsl:element>
  </xsl:template>  

  <xsl:template match="author">
    <node CREATED="1311176321071" ID="ID_1573932839" MODIFIED="1311176324722" TEXT="author">
      <node CREATED="1311176326649" ID="ID_451850163" MODIFIED="1311176329456" TEXT="name">
	<node CREATED="1311176352743" ID="ID_109036830" MODIFIED="1311234875553" STYLE="bubble">
	  <xsl:attribute name="TEXT"><xsl:value-of select="name"/></xsl:attribute>
	  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
	</node>
      </node>
      <node CREATED="1311176333060" ID="ID_896894920" MODIFIED="1311176335780" TEXT="email">
	<node CREATED="1311176383595" ID="ID_550027674" MODIFIED="1311234881668" STYLE="bubble">
	  <xsl:attribute name="TEXT"><xsl:value-of select="email"/></xsl:attribute>
	  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
	</node>
      </node>
      <node CREATED="1311176348053" ID="ID_1556413098" MODIFIED="1311176350924" TEXT="comment">
	<node CREATED="1311176394385" ID="ID_1447602460" MODIFIED="1311176456353" STYLE="bubble">
	  <xsl:attribute name="TEXT"><xsl:value-of select="comment"/></xsl:attribute>
	  <font ITALIC="true" NAME="SansSerif" SIZE="12"/>
	</node>
      </node>
    </node>
  </xsl:template> 

</xsl:stylesheet>
