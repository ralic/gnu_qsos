
class Sheet

	attr_accessor :id
	attr_accessor :root
  	attr_accessor :name
  
 	def initialize(rootItem=nil)
 		if ! rootItem.nil?
			@root = rootItem
		end
 	end
end