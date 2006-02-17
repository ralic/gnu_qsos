#Copyright (c) 2004 2005 2006 Atos Origin
#Permission is granted to copy, distribute and/or modify this
#document
#under the terms of the GNU Free Documentation License,
#      Version 1.2
#      or any later version published by the Free Software
#      Foundation;
#      with no Invariant Sections, no Front-Cover
#      Texts, and no Back-Cover
#      Texts.  A copy of the license is
#      included in the section entitled "GNU
#      Free Documentation License".
#
# $Id: Makefile,v 1.2 2006/02/17 10:27:16 goneri Exp $
TEX=changelog.tex define.tex eval.tex eval_app.tex fdl.tex general_process.tex glossary.tex internet_site.tex intro.tex license.tex manifest.tex qsos.tex qualify.tex
# TODO split this by language :
GFXS_DEST_PNG=images/comparaison.eps images/processus_4_etapes.eps images/definir.eps images/evaluer.eps images/qualifier.eps images/selectionner.eps images/QSOS.eps images/fr/comparaison.eps images/fr/processus_4_etapes.eps images/fr/definir.eps images/fr/evaluer.eps images/fr/qualifier.eps images/fr/selectionner.eps images/fr/QSOS.eps images/es/comparaison.eps images/es/processus_4_etapes.eps images/es/definir.eps images/es/evaluer.eps images/es/qualifier.eps images/es/selectionner.eps images/es/QSOS.eps


GFXS_DEST_SVG=
WWW=../www
CURRENT_RELEASE=1.5
DIST_FILES=clean_html.pl Makefile README $(TEX)
GFXS_SRC_PNG= $(GFXS_DEST_PNG:.eps=.png)
GFXS_SRC_SVG= $(GFXS_DEST_SVG:.eps=.svg)


dvi-en: qsos-$(CURRENT_RELEASE)-en.dvi
qsos-$(CURRENT_RELEASE)-en.dvi: $(GFXS_DEST_PNG) $(GFXS_DEST_SVG) $(TEX)
	rm *.aux
	latex qsos.tex
	latex qsos.tex
	mv qsos.dvi qsos-$(CURRENT_RELEASE)-en-dvi
 
dvi-fr: qsos-$(CURRENT_RELEASE)-fr.dvi
qsos-$(CURRENT_RELEASE)-fr.dvi: $(GFXS_DEST_PNG) $(GFXS_DEST_SVG) $(TEX)
	rm *.aux
	latex lang/fr/qsos.tex
	latex lang/fr/qsos.tex
	mv qsos.dvi qsos-$(CURRENT_RELEASE)-fr.dvi

dvi-es: qsos-$(CURRENT_RELEASE)-es.dvi
qsos-$(CURRENT_RELEASE)-es.dvi: $(GFXS_DEST_PNG) $(GFXS_DEST_SVG) $(TEX)
	rm *.aux
	latex lang/es/qsos.tex
	latex lang/es/qsos.tex
	mv qsos.dvi qsos-$(CURRENT_RELEASE)-es.dvi



html: $(GFXS_DEST_PNG) $(GFXS_DEST_SVG)
#	hevea qsos.hva hyperref.hva -pedantic -charset iso-8859-1 qsos.tex
#	imagen -png qsos
#	TODO : Split that crappy code 
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation qsos.tex
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation qsos.tex
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation qsos.tex
	latex2html -dir qsos -no_footnode -auto_prefix -split 0 -info 0 -no_navigation license.tex
	latex2html -dir qsos -no_footnode -auto_prefix -split 0 -info 0 -no_navigation fdl.tex
	mv qsos qsos_en
	#French
	mkdir qsos
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/fr/qsos.tex
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/fr/qsos.tex
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/fr/qsos.tex
	latex2html -dir qsos -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/fr/license.tex
	latex2html -dir qsos -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/fr/fdl.tex
	mv qsos qsos_fr
	# Spanish
	mkdir qsos
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/es/qsos.tex
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/es/qsos.tex
	latex2html -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/es/qsos.tex
	latex2html -dir qsos -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/es/license.tex
	latex2html -dir qsos -no_footnode -auto_prefix -split 0 -info 0 -no_navigation lang/es/fdl.tex
	mv qsos qsos_es
	mkdir qsos
	mv qsos_en qsos/en
	mv qsos_fr qsos/fr
	mv qsos_es qsos/es
	# latex2html fait du code crade, je passe un coup de tidy pour nettoyer le code
	# les parentheses autour invrse le code de retour de tidy sinon make s'arrete en
	# pensant avoir une erreur fatale
	perl -e 'system("cat qsos/en/license.html | tidy -m -asxml  -clean > qsos/en/license_clean.html");1';
	perl -e 'system("cat qsos/en/qsos.html | tidy -m -asxml  -clean > qsos/en/qsos_clean.html");1';
	perl -e 'system("cat qsos/en/fdl.html | tidy -m -asxml  -clean > qsos/en/fdl_clean.html");1';
	mv qsos/en/license_clean.html qsos/en/license.html
	mv qsos/en/qsos_clean.html qsos/en/qsos.html
	mv qsos/en/fdl_clean.html qsos/en/fdl.html
	#French
	perl -e 'system("cat qsos/fr/licfrse.html | tidy -m -asxml  -clean > qsos/fr/licfrse_clean.html");1';
	perl -e 'system("cat qsos/fr/qsos.html | tidy -m -asxml  -clean > qsos/fr/qsos_clean.html");1';
	perl -e 'system("cat qsos/fr/fdl.html | tidy -m -asxml  -clean > qsos/fr/fdl_clean.html");1';
	mv qsos/fr/licfrse_clean.html qsos/fr/licfrse.html
	mv qsos/fr/qsos_clean.html qsos/fr/qsos.html
	mv qsos/fr/fdl_clean.html qsos/fr/fdl.html
	#Spanish
	perl -e 'system("cat qsos/es/licesse.html | tidy -m -asxml  -clean > qsos/es/licesse_clean.html");1';
	perl -e 'system("cat qsos/es/qsos.html | tidy -m -asxml  -clean > qsos/es/qsos_clean.html");1';
	perl -e 'system("cat qsos/es/fdl.html | tidy -m -asxml  -clean > qsos/es/fdl_clean.html");1';
	mv qsos/es/licesse_clean.html qsos/es/licesse.html
	mv qsos/es/qsos_clean.html qsos/es/qsos.html
	mv qsos/es/fdl_clean.html qsos/es/fdl.html


clean:
	rm -rf  $(GFXS_DEST_PNG) $(GFXS_DEST_SVG) \
		qsos qsos_* \
		*.aux *.dvi *.log *.toc \
		qsos-$(CURRENT_RELEASE)* \
		*.out *.pdf

license: fdl.tex
	latex fdl.tex

pdf: $(GFXS_DEST_PNG) $(GFXS_DEST_SVG)
	rm *.aux
	pdflatex qsos.tex
	mv qsos.pdf qsos-$(CURRENT_RELEASE)-en.pdf
	rm *.aux
	pdflatex lang/fr/qsos.tex
	mv qsos.pdf qsos-$(CURRENT_RELEASE)-fr.pdf
	pdflatex lang/es/qsos.tex
	mv qsos.pdf qsos-$(CURRENT_RELEASE)-es.pdf
	pdflatex fdl.tex

%.eps: %.png
	convert $^ $@ 
 
%.eps: %.svg
	inkscape $^ -E $@

tgz: qsos-$(CURRENT_RELEASE).tar.gz
qsos-$(CURRENT_RELEASE).tar.gz:
	mkdir -p qsos-$(CURRENT_RELEASE)/images/fr
	mkdir -p qsos-$(CURRENT_RELEASE)/images/es
	mkdir -p qsos-$(CURRENT_RELEASE)/lang/fr
	mkdir -p qsos-$(CURRENT_RELEASE)/lang/es
	cp $(DIST_FILES) qsos-$(CURRENT_RELEASE)/
	cp lang/fr/$(DIST_FILES) qsos-$(CURRENT_RELEASE)/lang/fr
	cp lang/es/$(DIST_FILES) qsos-$(CURRENT_RELEASE)/lang/es
	cp images/*.png images/README qsos-$(CURRENT_RELEASE)/images/
	cp images/fr/*.png images/README qsos-$(CURRENT_RELEASE)/images/fr
	cp images/es/*.png images/README qsos-$(CURRENT_RELEASE)/images/es
	tar cfz qsos-$(CURRENT_RELEASE).tar.gz qsos-$(CURRENT_RELEASE)


install: dvi-fr dvi-en html license pdf tgz 
	mkdir -p $(WWW)/methode/en
	cp -v qsos/en/*.png $(WWW)/methode/en
	./clean_html.pl qsos/en/qsos.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/en/methode.html
	./clean_html.pl qsos/en/license.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/en/license.html
	./clean_html.pl qsos/en/fdl.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/en/fdl.html
	cp qsos-en.pdf $(WWW)/download/qsos-$(CURRENT_RELEASE)-en.pdf
	cp qsos-en.dvi $(WWW)/download/qsos-$(CURRENT_RELEASE)-en.dvi
	# french
	mkdir -p $(WWW)/methode/fr
	cp -v qsos/fr/*.png $(WWW)/methode/fr
	./clean_html.pl qsos/fr/qsos.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/fr/methode.html
	./clean_html.pl qsos/fr/licfrse.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/fr/licfrse.html
	./clean_html.pl qsos/fr/fdl.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/fr/fdl.html
	cp qsos-fr.pdf $(WWW)/download/qsos-$(CURRENT_RELEASE)-fr.pdf
	cp qsos-fr.dvi $(WWW)/download/qsos-$(CURRENT_RELEASE)-fr.dvi
	# spanish 
	mkdir -p $(WWW)/methode/es
	cp -v qsos/es/*.png $(WWW)/methode/es
	./clean_html.pl qsos/es/qsos.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/es/methode.html
	./clean_html.pl qsos/es/licesse.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/es/licesse.html
	./clean_html.pl qsos/es/fdl.html| recode ISO-8859-1..UTF-8 > $(WWW)/methode/es/fdl.html
	cp qsos-es.pdf $(WWW)/download/qsos-$(CURRENT_RELEASE)-es.pdf
	cp qsos-es.dvi $(WWW)/download/qsos-$(CURRENT_RELEASE)-es.dvi
	cp qsos-$(CURRENT_RELEASE).tar.gz $(WWW)/download/qsos-$(CURRENT_RELEASE).tar.gz
	cp fdl.pdf fdl.dvi $(WWW)/download
