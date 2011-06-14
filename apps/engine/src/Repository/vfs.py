"""
QSOS-Engine's repository utility.

This module provides the repository abstraction layer used by the Engine
"""


##
#    @defgroup vfs Virtual File System 
#    @ingroup Repository
#    @author Hery Randriamanamihaga

import git


def init():
    """
    Initiate a transaction on the repository
    
    Just call each 'init' method of the different repository implementations
    """
    git.init()
        
def commit(message):
    """
    Terminate a transaction on the repository
    
    Just call each 'commit' method of the different repository implementations
            
    @param message
        a commit message
    """
    git.commit(message)

def add(item, target):
    """
    Add an item to the repository
    
    Just call each 'add' method of the different repository implementations
    
    @param item 
        the item content's to add
    
    @param target
        the target location of the @param item into the repository.
        Must be a list containing the target file (the last element) and the different subfolders leading to it

    """
    git.add(item, target)
    
def log(item=""):
    """
    Show the history log of an item
    
    @param item
        the item to be show the log. If not provided, the complete history of the
        repository will be shown
        
    @return
        The history of the item
    """
    return git.log(item)