#
#	Created by RPELISSE
#
#	This interpreter understant the following command
#		- s[how] 'node' ( displays all the data on one node)
#		- b[rowse] 'node' ( display childs )
#		- a[dd] 'nodeA' 'nodeB' ( add nodeA as a child of nodeB
#		- r[emove] nodeA ( remove the node&childs from the tree, ask for confirmation)
#		- s[ave]
#		- n[ew] 
#		- o[pen] ( open file, close current file if any)
#		- q[uit] ( quit the app )
#
require 'rexml/document'
require 'querysos/QuerySOSException'

module QuerySOS

class CommandInterpreter

	attr_accessor	:configFile
	attr_accessor	:configurator
	attr_accessor	:commands
	attr_accessor	:current_cmd
	attr_accessor	:worker


	def initialize
		@configFile= nil
		@current_cmd = QuerySOS::Command.new
		@current_cmd.id = "" 
	end 
	# building command interpreter, loading knowncommand from config file
	def init
		# find the conf file
		if @configFile.nil? 
			# TODO: do this in a better way !
			@configFile= "querysos/commands.xml"
		end 
		conf = File.new(@configFile,"r")
		# Configuring the interpreter
		if @configurator.nil?
			throw QuerySOS::QuerySOSException.new("No configurator setted !")
		end
		@configurator.configFilePath= @configFile
		# load the command and define the prompt
		@commands = @configurator.load
		@prompt = @configurator.prompt
		if @prompt.nil? || @prompt != "" 
			@prompt  = "> "
		end
		# Checking that FileManager dependency has been setted
		if @worker.nil? || @worker.class != QuerySOS::Worker
			throw QuerySOS::QuerySOSException.new("No worker setted !")
		end
		# init the current_command
		@current_cmd = QuerySOS::Command.new
	end
	
	def executeCurrentCmd
		@worker.execute( @current_cmd )
	end

	def run
		while @current_cmd.nil? || @current_cmd.id != 'q'
			@current_cmd = cmd_line = nil
			print @prompt
			cmd_line = gets
			puts cmd_line
			if ! cmd_line.nil? && cmd_line != "" 
				@current_cmd = cmdIdentification(cmd_line.downcase.split)
				if ! @current_cmd.nil?
					self.executeCurrentCmd
				end
			end
		end 
	end
	public	:run

	def cmdIdentification(tab)
		# check arguments
		if tab.nil? || tab.class != Array
			throw Exception.new("Invalid argument, argument 1 can't be null and should be an instance of Array")
		end
		# First item should be the command or one of it's abreviated version
		@commands.each do |command|
			# start with the id...
			if ( tab[0].index(command.id) == 0 )
				# build the appropriate array
				command.args = tab[1,tab.length]
				return prepare(command)
			end
		end
	end

	def prepare(command)
		if command.nil? || command.class != QuerySOS::Command
			throw Exception.new("Invalid argument, argument 1 can't be null and should be an instance of Command")
		end
		invalid = false
		# can the args's array be empty ?
		if command.minArgs == 0 || command.maxArgs == 0 
			if command.args.size == 0
				return command
			else
				invalid = true
			end	
		end
		# validating arguments
		if	command.args.length > command.maxArgs.to_i || 
			command.args.length < command.minArgs.to_i
			invalid = true
		end
		# dealing with invalid input
		if invalid 
			puts "Error : Wrong numbers of arguments..."
			puts command.doc
			return nil
		else
			return command
		end
	end

end
end
