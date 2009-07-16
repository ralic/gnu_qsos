##
#@mainpage
#
#
#This is the new implementation of QSOS. Even though this version provides minimalistic
#features of final implementation, the whole submit and request processes are implemented
#
#@section installation Installation Guide
#@subsection configuration Workspace Configuration
#Running QSOS Engine requires the following softwares installed :
#- Python
#- Twisted  (required to run nevow web application framework)
#- Nevow (web application framework)
#- Doxygen (for automatic documentation generation, requires graphviz)
#
#All of these software are avalaible on recent GNU/Linux distribution on their package repository. 
#Therefore, the distrib package manager will be used to install them, for example on Ubuntu/Debian  :
#    aptitude install python twisted python-nevow doxygen graphviz
#@subsection clone Clone the repository
#- Clone the git repository :
#    - git clone git://git.savannah.nongnu.org/qsos.git
#@subsection run Run web application
#- Go into src dir
#- Update the twisted configuration file to point to your repository (Services/presentation.tac, line 11)
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
