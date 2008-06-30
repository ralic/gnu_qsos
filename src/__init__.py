##
#@mainpage
#
#
#This is the new implementation of QSOS. Even though this version provides minimalistic
#features of final implementation, the whole submit and request processes are implemented
#
#@section installation Installation Guide
#@subsection configuration Workspace Configuration
#- Python : install it using the distrib package manager
#    (for debian, apt-get install python)
#- Twisted  (twisted is required to run nevow web application framework) :
#    - Use distrib package manager twisted
#- Nevow (web application framework)
#    - Download sources from Nevow wiki : http://divmod.org/trac/attachment/wiki/SoftwareReleases/Nevow-0.9.31.tar.gz?format=raw
#    - Uncompress tarball
#    - Run installation script from uncompressed tarball: python setup.py install
#- Doxygen (for automatic documentation generation)
#    - Install using distrib package manager : apt-get install doxygen
#@subsection clone Clone the repository
#- Clone the git repository :
#    - git clone git://git.savannah.nongnu.org/qsos.git
#@subsection run Run web application
#- Go into src dir
#- Run presentation twisted application
#    - twistd -noy Services/presentation.tac
#@subsection Generate Generate Documentation
#- Go into doc dir
#- Run doxygen
#    - doxygen DoxyFile
#
#@see
#    - http://www.qsos.org/
#    - https://savannah.nongnu.org/projects/qsos/
#
#@todo
#    - Error package : everything
#    - Log package : everything
#    - Engine package :
#        - core module implementation
#        - ...
#    - Services package :
#        - Interaction with Engine through the core module
#        - ...
#    - Repository package : everything
#