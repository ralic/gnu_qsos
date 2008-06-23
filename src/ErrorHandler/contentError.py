"""
Content error handler
"""

##
#    @defgroup formatError Format Error
#    @ingroup ErrorHandler 
#    @author Hery Randriamanamihaga

from ErrorHandler.QSOSError import QSOSError as QError

class ContentError(QSOSError):
    """
    Format Error object
    """
    def __init__(self, error, logger=None):
        QError.__init__(self, logger)
        self.error = error

    def __str__(self):
        return "Content Error encountered : %s" % (self.error, )
    