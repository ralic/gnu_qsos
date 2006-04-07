/* $Id: LibQSOSTest.java,v 1.1 2006/04/07 12:04:08 aclerf Exp $
*
*  Copyright (C) 2006 Atos Origin 
*
*  Author: Arthur Clerfeuille <arthur.clerfeuille@atosorigin.com>
*
*  This program is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  You should have received a copy of the GNU General Public License
*  along with this program; if not, write to the Free Software
*  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/
package com.ao.o3s.test;

import java.net.MalformedURLException;
import java.net.URL;

import com.ao.o3s.utils.ILibQSOS;
import com.ao.o3s.utils.LibQSOS;

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
		lib.setQsosappfamily("groupware mais pas fiable");
		lib.setQsosspecificformat("666");
		
		lib.delAuthor("Gonéri");
		lib.addAuthor("Arthur Clerfeuille","clerfeui@enseirb.fr");
		
		/* Modification des elements */
		//lib.setCommentByName("popularity","");
		
		
		
		lib.write("test/testModif.xml");
		
		
	}
	
	
}
