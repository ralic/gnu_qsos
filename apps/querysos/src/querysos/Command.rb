#
#	Created by RPELISSE
#
#	This class is a simple "bean", it contains all info related to a Command
#
#	
#
module QuerySOS

class Command

	attr_accessor	:id
	attr_accessor	:name
	attr_accessor	:doc
	attr_accessor	:minArgs
	attr_accessor	:maxArgs
 	attr_accessor	:args
	attr_accessor	:code

	#
	#	This method allow to set any property of the bean. 
	#	Use this method to set property, as it's ensure validtion
	#
	#		
	def setMySelf(value,id)
                if id.nil?
                        throw QuerySOS::QuerySOSException.new("Can't set bean without a value (" + value + ").)")
                end
                if ! id.nil?
                        if self.respond_to?(id)
                                setter = self.method(id+"=")
                                setter.call(value)
                        end
                end
	end
end

end