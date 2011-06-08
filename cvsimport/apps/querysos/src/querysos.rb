#!/usr/bin/env ruby
#require 'getoptlong'

require 'querysos/CommandInterpreter'
require 'querysos/XMLCommandConfigurator'
require 'querysos/Worker'
require 'querysos/FileManager'

#options = GetoptLong.new(ARGV)
#puts options.inspect

puts "Welcome to QuerySOS 1.0 - A command line editor for QSOS Sheet"
interpreter = QuerySOS::CommandInterpreter.new
interpreter.configurator = QuerySOS::XMLCommandConfigurator.new
w = QuerySOS::Worker.new
w.filemanager= QuerySOS::FileManager.new
interpreter.worker = w
interpreter.init
# improve by using getOptLong...
# for the moment argument can only be a sheet to open
if ARGV.size > 0 
	file = ARGV[0]
	if File.exists? file
		interpreter.commands.each do |command|
			if command.id == "o"
				command.args = Array.new
				command.args.push file
				interpreter.current_cmd = command	
				interpreter.executeCurrentCmd
			end
		end
	else
		puts "Can't open : " + file
	end
end
# let's go...
interpreter.run
