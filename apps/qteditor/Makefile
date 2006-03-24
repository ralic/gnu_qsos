PARSE=bin/parse.pl
DESTDIR ?=/usr
#PM_DIR=$(DESTDIR)/lib/site_perl/QSOS/QtEditor
PM_DIR=$(DESTDIR)/lib/perl5/site_perl/QSOS/QtEditor
BIN_DIR=$(DESTDIR)/bin


all: lib/qsosform.pm lib/propertyform.pm lib/aboutform.pm

lib/qsosform.pm: ui/qsosform.ui lib/qsosform.code.pl
	$(PARSE) ui/qsosform.ui lib/qsosform.code.pl lib/qsosform.pm

lib/propertyform.pm: ui/propertyform.ui lib/propertyform.code.pl
	$(PARSE) ui/propertyform.ui lib/propertyform.code.pl lib/propertyform.pm

lib/aboutform.pm: ui/aboutform.ui lib/aboutform.code.pl
	$(PARSE) ui/aboutform.ui lib/aboutform.code.pl lib/aboutform.pm

clean:
	rm lib/aboutform.pm lib/propertyform.pm lib/qsosform.pm
install: all
	mkdir -p $(PM_DIR)
	mkdir -p $(BIN_DIR)
	cp lib/qsosform.pm $(PM_DIR)/Qsosform.pm
	cp lib/propertyform.pm $(PM_DIR)/Propertyform.pm
	cp lib/aboutform.pm $(PM_DIR)/Aboutform.pm
	cp bin/qsos-qteditor $(BIN_DIR)/qsos-qteditor
	chmod 644 $(PM_DIR)/*.pm
	chmod 755 $(PM_DIR)
	chmod 755 $(BIN_DIR)/qsos-qteditor
