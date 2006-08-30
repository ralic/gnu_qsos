#
#	Created by RPELISSE
#
#
#
#
#
module QuerySOS
require 'rexml/document'
require 'querysos/Command'

class XMLCommandConfigurator

	attr_accessor	:configFilePath
	attr_accessor	:prompt
	
	def initialize
		@DOCUMENTATION = "Documentation"
		@ARGS = "args"
		@PROMPT = "prompt"

 		@ID = "id"
		@NAME = "name"
		@MIN = "min"
		@MAX = "max"
	end

	#
	#	Main method, allow to load the configuration of the command interpreter from the XML file.
	#
	#	@return: an instance of Array that containes each commands
 	def load
 		if @configFilePath.nil? || ! File.exists?(@configFilePath)
 			throw QuerySOS::QuerySOSException.new("Missing configuration file for the command interpreter")
 		end
 		file = File.new @configFilePath	
                doc = REXML::Document.new file
 		return getCommands(doc.root)
 	end
	#
	#	Setter for configFilePath. Check that the argument is a not nil and it's an instance of String 
	#	
	#	@param : file, configuration filename ( may include  the path)
	#
	def configFilePath=(file)
		if file.nil? || file.class != String
			throw QuerySOS::QuerySOSException.new("Invalid configuration file for the command interpreter: " + file.to_s)
		end
		@configFilePath=file
	end

	def getCommands(root)
		# validating argument 
		if root.nil? || root.class != REXML::Element 
			throw QuerySOS::QuerySOSException("Invalid argument, argument 1 must be an instance of REXML::Element")
		end
		# getting the prompt
		if ! root.attribute(@PROMPT).nil?
			@prompt = root.attribute(@PROMPT).value
		end
		# getting all the known commands
		commands = Array.new
		if root.has_elements?
			root.elements.each do |command|
				c = QuerySOS::Command.new
				c.setMySelf(command.attribute(@ID).value,@ID)
				c.setMySelf(command.attribute(@NAME).value,@NAME)
				if command.has_elements?
					command.elements.each do |property|
						extractCommandChilds(c,property)
					end
				end
				commands.push c
			end
		end		
		return commands
	end

	def extractCommandChilds(c,property)
		# extracting documentation ( if any)
		if property.name == @DOCUMENTATION && property.has_text?
			c.doc = property.get_text	
		end				
		# extracting Args property
		if property.name == @ARGS 
			c.minArgs = property.attribute(@MIN).value
			c.maxArgs = property.attribute(@MAX).value
		end
		# extracting script code !
		if property.name == @SCRIPT && property.has_text?
			c.code = property.get_text
		end
	end

end

end
