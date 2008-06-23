"""
QSOS Error handler
"""

##
#    @defgroup QSOSError QSOS Error
#    @ingroup ErrorHandler 
#    @author Hery Randriamanamihaga

class QSOSError(Exception):
    """
    Base class for exceptions raised by QSOS
    """
    
    
    def __init__(self, logger=None):
        """
        Class initializer.
        
        @param logger  
            Logger in which events are written
        """
        self.logger = logger