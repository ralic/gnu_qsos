"""
Format error handler
"""

##
#    @defgroup formatError Format Error
#    @ingroup ErrorHandler 
#    @author Hery Randriamanamihaga

from ErrorHandler.QSOSError import QSOSError as QError

class FormatError(QSOSError):
    """
    Format Error object
    """
    def __init__(self, error, logger=None):
        QError.__init__(self, logger)
        self.error = error

    def __str__(self):
        return "Format Error encountered : %s" % (self.error, )
    