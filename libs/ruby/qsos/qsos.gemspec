require 'rubygems'

spec = Gem::Specification.new do |s|
  s.name = 'qsos'
  s.version = '1.0.0'
  s.summary = "QSOS Ruby Library"
  s.description = %{Simple builder classes for using QSOS files.}
  s.platform = Gem::Platform::RUBY
  candidates = Dir.glob("{lib,tests}/**/*")
#  puts candidates.inspect
  s.files = candidates.delete_if do |item|
  	item.include?("CVS") || item.include?("rdoc)")
  	end
  s.require_path = 'lib'
  s.autorequire = 'document.rb'
  s.has_rdoc = false
  s.author = "Romain PELISSE"
  s.homepage="http://savannah.nongnu.org/projects/qsos/"
  s.test_file = "tests/testDocument.rb"  
  s.email = 'romain.pelisse@atosorigin.com'
end
