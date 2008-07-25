"""
Submit module.

This module handles the repository submit page.
"""
##
#    @defgroup  submit Submit
#    @ingroup Services
#    @author Hery Randriamanamihaga
#    @todo 
#        Handle "dynamic" docFactory using Stan in UploadPage
#

from twisted.application    import service
from twisted.application    import strports

from nevow                  import appserver
from nevow                  import loaders
from nevow                  import rend
from nevow                  import static
from nevow                  import url
from nevow                  import tags as T
from nevow import inevow

from formless               import annotate
from formless               import webform 

from Engine                 import splitter
from Engine import  core
from QSOSpage import QSOSPage
from QSOSpage import DefaultPage

##
#    @ingroup submit
#
class ConfirmationPage(DefaultPage):
    """
    Renders confirmation page.
    
    Handles the confirmation page of a successful evaluation upload.
    Return to root page or submit another evaluation are suggested.
    """
    def renderBody ( self, ctx, data ):
        return T.body [ T.h1 [ "Your evaluation has been uploaded" ] ,
             T.a ( href = "/" ) [ "Go to Repository Main Page" ],
             " or ",
             T.a ( href = "/submit" ) [ "Upload another evaluation" ]
            ]
        
    def renderTitle(self, ctx, data):
        return "QSOS Upload Page Confirmation"
    
##
#    @ingroup submit
#
class UploadPage(DefaultPage):
    """
    Handles upload page.
    
    This class renders the main page of submit branch of the site.
    
    """
    def renderBody (self, ctx, data):
        return T.body[webform.renderForms()]
    
    def renderTitle(self, ctx, data):
        return "QSOS Upload Page"

    def submitEvaluation(self, **formData):
        "Put the uploaded evaluation into the local repository"
        core.show(['TODO : check', formData['E-mail']])
        core.show(['TODO : check', formData['Author']])
        core.show(['TODO : check', formData['Description']])
        core.show(['TODO : exploit', formData['Type']])
        core.show(['TODO : exploit', formData['File']])
        return url.here.child('confirmation')

    def bind_submitEvaluation(self, ctx):
        "Bind the proper action to perform when submit action is invoked"
        return [
                ('Author', annotate.String()),
                ('E-mail', annotate.String()),
                ('Description', annotate.Text()),
                ('Type', annotate.Choice(['Evaluation','Template', 'Family'])),
                ('File', annotate.FileUpload(required=False))
                        ]
    def child_confirmation(self, ctx):
        return ConfirmationPage(self.repository)

