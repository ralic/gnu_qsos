#
#	Created by RPELISSE
#
#
#
#
#
module QuerySOS

require 'querysos/Command'

class Worker
	
	attr_accessor	:filemanager

	def execute(command)
		# Validating the argument
		if command.nil? || command.class != QuerySOS::Command
			throw Exception.new("Invalid argument, argument 1 can't be nil and must be an instance of Command")
		end
		# 
		if command.id == "q"
			if ! @filemanager.current_sheet.nil? 
				@filemanager.close
			end
			puts "Bye..."
			exit
		elsif command.id == "o"
			file = command.args[0]
			puts "Opening : " + file
			if ! @filemanager.current_sheet.nil?
				@filemanager.close
			end
			begin
				@filemanager.open file
			rescue
				puts $!.message
				return
			end
			# displaying some basic info about the opened sheet
			puts "Product:" + @filemanager.current_sheet.getAppname
			puts "Lang:"	+ @filemanager.current_sheet.getLanguage
			puts "Release:"	+ @filemanager.current_sheet.getRelease
			puts "Website:"	+ @filemanager.current_sheet.getUrl
			puts "Demo:"	+ @filemanager.current_sheet.getDemoUrl
			puts ""
		elsif command.id == "n"
			puts "Creating a new file..."
			@filemanager.new command.args[0]
		elsif command.id == "w"
			puts "saving..."
			if ! @filemanager.current_sheet.nil?
				@filemanager.save
			else
				puts "No file opened : abort saving."
			end
		elsif command.id == "b"
			puts "Browsing node " + command.args[0].to_s + "..."
			if @filemanager.current_sheet
				@filemanager.current_sheet.
			else
				puts "No sheet open !"
			end
		elsif command.id == "s"
			puts "Displaying data on node " + command.args[0].to_s + "..."
			if ! @filemanager.current_sheet.nil?
				node = command.args[0].to_s
				puts "Field: " + node 
#				puts @filemanager.current_sheet.getDescByName(node,0)
#				puts @filemanager.current_sheet.getDescByName(node,1)
#				puts @filemanager.current_sheet.getDescByName(node,2)
				puts "Comment: " + @filemanager.current_sheet.getCommentByName(node)
				puts "Score: " + @filemanager.current_sheet.getScoreByName(node)
			else
				puts "No sheet open !"
			end
		end


	end
end
end