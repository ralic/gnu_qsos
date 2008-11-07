"""
Semantic error handler
"""

##
#    @defgroup SemanticError Semantic Error
#    @ingroup ErrorHandler 
#    @author Hery Randriamanamihaga

from ErrorHandler.QSOSError import QSOSError as QError

class SemanticError(QSOSError):
    """
    Semantic Error object
    """
    def __init__(self, error, logger=None):
        QError.__init__(self, logger)
        self.error = error

    def __str__(self):
        return "Semantic Error encountered : %s" % (self.error, )
    