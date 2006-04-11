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
 * @author aclerf
 *
 */
public class LibQSOSTest extends TestCase {
	private ILibQSOS lib;
	
	public void testGet() throws MalformedURLException{
		/*Creation de l'objet*/
		lib = new LibQSOS();
		/*Initialisation de l'objet*/
		lib.load(new URL("http://cvs.savannah.gnu.org/viewcvs/*checkout*/qsos/qsos/sheet/groupware/kolab/kolab.qsos"));
		
		/*Verification des valeurs de l'entete*/
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
		assertEquals("qsosspecificformat",lib.getQsosspecificformat(),"1");
		System.out.println(lib.getAuthors());
		
		
		/*Search by name on an element */
		assertEquals("Desc0","Very few users identified",lib.getDescByName("popularity",0));
		assertEquals("Desc1","Detectable use on Internet",lib.getDescByName("popularity",1));
		assertEquals("Desc2","Numerous users, numerous references",lib.getDescByName("popularity",2));
		assertEquals("Score","2",lib.getScoreByName("popularity"));
	}
	
	public void testSet() throws MalformedURLException{
		/*Creation de l'objet*/
		lib = new LibQSOS();
		/*Initialisation de l'objet*/
		lib.load(new URL("http://cvs.savannah.gnu.org/viewcvs/*checkout*/qsos/qsos/sheet/groupware/kolab/kolab.qsos"));

			
		lib.setLanguage("fr");
		lib.setAppname("kolab modifié par les tests");
		lib.setRelease("2.1");
		lib.setLicenseId("23");
		lib.setLicenseDesc("la license correspondante");
		lib.setUrl("www.google.fr");
		lib.setDesc("Fiche modifiés par des tests");
		lib.setDemoUrl("www.yahoo.fr");
		lib.setQsosformat("457812");
		lib.setQsosappfamily("groupware");
		lib.setQsosspecificformat("666");
		
		lib.delAuthor("Gonéri");
		lib.addAuthor("Arthur Clerfeuille","clerfeui@enseirb.fr");
		
		/* Modification des elements */
		lib.setCommentByName("popularity","");
		
		List<SimpleMenuEntry> list = lib.getSimpleTree();
		System.out.println(lib.Debugaffichage(list));
		lib.write("test/testModif.xml");
		
		
	}
	
	
}
