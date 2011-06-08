require 'rubygems'
require_gem 'qsos'

require 'querysos/QuerySOSException'

module QuerySOS
class FileManager

	public
	attr_accessor 	:current_sheet
	attr_reader 	:filename
	attr_accessor	:lib
	attr_reader	:filelock

	#
	#	Basic constructor : simply load the qsos library.
	#
	#
	def initialize
		self.init
		@lib = QSOS::Document.new		
	end

	def init
		@filename = ""
		@current_sheet = nil
	end


	def sheetValid?(filename)
		# Is the filename nil ?
		if filename.nil? 
			throw QuerySOSException.new("Filename provided is 'nil'")
		end
		# Is the filename a proper String ?
		if filename.class != String
			throw QuerySOSException.new("Filename provided must be a String,not a " + filename.class.to_s)
		end
		# Does the file already exists ?
		if ! File.exists? filename 
			throw QuerySOSException.new("File : " + filename + " does not exists.") 
		end
		# TODO : Add more validation ( valid xml ? ...)
		# Ok, valid file
		return true		
	end

	#	
	# 	Creates new a qsos file. Check for the extention .qsos, and add it if missing
	#
	#	@param: filename, string containing the new filename ( plus possible path)
	def new(filename)
		if filename.nil?
			throw QuerySOSException.new("Argument 1 must not be nil !")
		end
		if filename.class != String
			throw QuerySOSException.new("Argument 1 expected is to be a String, not a " + filename.class.to_s)
		end
		if  File.exists? filename
			throw QuerySOSException.new("The file " + filename + " already exists.")
		end	
	end

	#
	#	Open the file passed as argument : check if the file exists, if its an url ,try to download it.
	#	then, it put a lock on it. 
	#
	#	@exception : Raise an QuerySOSException if the file exists ( or has been successfully downloaded), but
	#			failed to open it.
	def open(filename)
		if self.sheetValid? filename
			@filename = filename
			# Create a lock on the file
			self.lock
			# attempt to load the file 
			begin
				@current_sheet = @lib.load filename
			rescue
				# load file, cleaning resources and object properties
				self.init
				self.unlock
				throw QuerySOS::QuerySOSException.new("Loading file '" + filename + "' has failed !")
			end
		end
	end
	public :open

	def lock
		if @filelock.nil?
			@filelock = File.new(@filename,File::RDWR)
			if ! @filelock.flock(File::LOCK_EX)
				throw QuerySOS::QuerySOSException("File " + @filename + " already locked")
			end
		else
			throw QuerySOS::QuerySOSException.new("Trying to lock a file without releasing this file :" + @firelock.pathname + @firelock.basename + ".")
		end
	end
	
	def unlock
		if ! @filelock.nil?
			if ! @filelock.flock(File::LOCK_UN)
				throw QuerySOS::QuerySOSException("Trouble unlocking file: " + @firelock.basename)
			end
			@filelock = nil
		else
			throw QuerySOS::QuerySOSException.new("Trying to unlock a file, without previous lock.")
		end
	end


	#
	#	Close the current file : unlock it, and reset itself.
	#
	#
	def close
		if self.loaded?
			# TODO a file is loaded, try to unlock it...
			self.init
			self.unlock
		else
			throw QuerySOSException.new("Try to close file as no file was loaded.")
		end
	end
	public :close

	#
	# check if a file is already loaded. Return false if no file is loaded, otherwise true
	#
	def loaded?
		! @current_sheet.nil? && ! @filename.nil? && @filename != ""	
	end
	


	def save
		if self.loaded?
			lib.write @filename
		else
			throw QuerySOS::QuerySOSException.new("Trying to save while no file opened")
		end
	end
	public :save

	def getSheet(path)
		# TODO: check for url, download in case of url
	end
	private :getSheet
end
end