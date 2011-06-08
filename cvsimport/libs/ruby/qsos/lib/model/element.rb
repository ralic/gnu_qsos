module QSOS
class Element

public
  attr_accessor :id
  attr_accessor :meta
  attr_accessor :name
  attr_accessor :title
  attr_accessor :childs
  attr_accessor :parent
  attr_accessor :desc0
  attr_accessor :desc1
  attr_accessor :desc2
  attr_accessor :score
  attr_accessor :comment
  attr_accessor :text
  

  public 
  
	def initialize(name="",score="",comment="",text="",meta="element")
		@name = name
		@score = score
		@comment = comment
		@text = text
		@meta = meta
		@childs = Array.new
	end
	  
	def setMySelf(value,id)
		if id.nil? || value.nil?
			# TODO: throw an exception
			return
		end
		if ! id.nil?
			if self.respond_to?(id)
				setter = self.method(id+"=")
				setter.call(value)
			end
		end
	end
	
	def getMySelf(id)
		if ! id.nil?
			if self.respond_to?(id)
				return self.send(id)
			end
		end
		# delete what below
	end
	  
	def desc
	#	if 	! self.desc0.nil? &&
	#		! self.desc1.nil? &&
	#		! self.desc2.nil? 	
	#		return "#{self.desc0} + #{self.desc1} + #{self.desc2}"
	#	end
		return ""
	end
	  
	def addChild(child)
		@childs.push(child)
	end

	# TODO: implements this...
	#def inspect()
	#end
	
	def delElement(toDelete)
		if toDelete.class != Element
			# throw an exception TODO !
		end
		#implements this
	end
	
end
end