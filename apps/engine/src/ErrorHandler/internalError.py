"""
Internal error handler
"""

##
#    @defgroup InternalError Internal Error
#    @ingroup ErrorHandler 
#    @author Hery Randriamanamihaga

from ErrorHandler.QSOSError import QSOSError as QError

class InternalError(QSOSError):
    """
    Internal Error object
    """
    def __init__(self, error, logger=None):
        QError.__init__(self, logger)
        self.error = error

    def __str__(self):
        return "Internal Error encountered : %s" % (self.error, )
    