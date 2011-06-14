/**
 * 
 */
package org.qsos.test;

import java.net.MalformedURLException;
import java.net.URL;
import java.util.List;

import org.qsos.utils.ILibQSOS;
import org.qsos.utils.LibQSOS;
import org.qsos.utils.SimpleMenuEntry;


import junit.framework.TestCase;

/**
 * @author Arthur Clerfeuille <arthur.clerfeuille@atosorigin.com>
 *
 */
public class LibQSOSTest extends TestCase {
	private ILibQSOS lib;
	
	public void testGet() throws MalformedURLException{
		/*Creation of the lib which allow to manipulate datas*/
		lib = new LibQSOS();
		/*Instanciation with a sheet*/
		lib.load(new URL("http://cvs.savannah.gnu.org/viewcvs/*checkout*/qsos/qsos/sheet/groupware/kolab/kolab.qsos"));
		
		/* Check of datas */
		assertEquals("language",lib.getLanguage(),"en");
		assertEquals("appname",lib.getAppname(),"kolab");
		assertEquals("release",lib.getRelease(),"2");
		assertEquals("licenseid",lib.getLicenseId(),"31");
		assertEquals("licensedesc",lib.getLicenseDesc(),"GNU General Public License");
		assertEquals("url",lib.getUrl(),"http://www.kolab.org");
		assertEquals("des",lib.getDesc(),"Kolab is a groupware used in the german administration");
		assertEquals("demourl",lib.getDemoUrl(),"http://kolab.org/screenshots.html");
		assertEquals("qsosformat",lib.getQsosformat(),"1");
		assertEquals("qsosappfamily",lib.getQsosappfamily(),"groupware");
		assertEquals("qsosappname",lib.getQsosappname(),"kolab");
		assertEquals("qsosspecificformat",lib.getQsosspecificformat(),"1");
		System.out.println(lib.getAuthors());
		
		
		/*Search by name on an element */
		assertEquals("Desc0","Very few users identified",lib.getDescByName("popularity",0));
		assertEquals("Desc1","Detectable use on Internet",lib.getDescByName("popularity",1));
		assertEquals("Desc2","Numerous users, numerous references",lib.getDescByName("popularity",2));
		assertEquals("Score","2",lib.getScoreByName("popularity"));
	}
	
	public void testSet() throws MalformedURLException{
		/*Creation*/
		lib = new LibQSOS();
		/*Instanciation with a template*/
		lib.load(new URL("http://cvs.savannah.gnu.org/viewcvs/*checkout*/qsos/qsos/sheet/groupware/template/groupware.qsos"));

		/*Adding datas in the header*/	
		lib.setLanguage("en");
		lib.setAppname("kolab");
		lib.setRelease("2.1");
		lib.setLicenseId("31");
		lib.setLicenseDesc("GNU General Public License");
		lib.setUrl("http://www.kolab.org");
		lib.setDesc("Kolab is a groupware used in the german administration");
		lib.setDemoUrl("http://kolab.org/screenshots.html");
		lib.setQsosformat("1");
		lib.setQsosappfamily("groupware");
		lib.setQsosappname("kolab");
		lib.setQsosspecificformat("1");
		
		lib.addAuthor("Gonéri Le Bouder","goneri.lebouder@atosorigin.com");
		
		/* Adding data in the sections but won't work till the update of the template*/
		lib.setScoreByName("age","2");
		lib.setCommentByName("age","Kolab.org domain was created the 29th of Oct 2002");
		
		
		/* Getting a tree view of the file */
		List<SimpleMenuEntry> list = lib.getSimpleTree();
		System.out.println(lib.Debugaffichage(list));
		
		/* Writing the file */
		lib.write("test/testKolabCreation.xml");
	}
	
	
}
