"""
QSOS-Engine's repository utility.

This module provides the repository abstraction layer used by the Engine
"""


##
#    @defgroup git Git 
#    @ingroup Repository
#    @author Hery Randriamanamihaga

import os

def init():
    os.popen('git pull')
    
def commit(message):
    os.popen("git commit -m \"%s\"" % message)
    os.popen('git push')

def add(content, target):
    #format the path to the target file
    path = os.path.join(*target)
    #Create the file with the proper conent, ensuring
    #that all necessary folders exists
    try :
        os.makedirs(os.path.split(path)[0])
    except OSError :
        pass
    file = open(path,'w')
    file.write(content)
    file.close()
    os.popen("git add %s" % path)
