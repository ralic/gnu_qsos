/* $Id: ILibQSOS.java,v 1.4 2006/04/13 12:57:37 aclerf Exp $
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
package org.qsos.utils;

import java.net.URL;
import java.util.List;

import org.qsos.data.ISheet;

/**This interface should be implemented by LibQSOS.
 * It contains all the methods of the api defined to make editors.
 * @author Arthur Clerfeuille <arthur.clerfeuille@atosorigin.com>
 *
 */
public interface ILibQSOS {
	/**
	 * Allows to load the java model corresponding to the xml document found at the 
	 * url.
	 * @param url the URL where the xml document is get.
	 */
	 public void load(URL url);
	
	/**
	 * Allows to get the description number numDesc of the element called name.
	 * @param name the name to search.
	 * @param numDesc int representing the number of the description to search.
	 * @return a String corresponding to the description asked.
	 */
	 public String getDescByName(String name, int numDesc);

	/**
	 * Allows to set a comment to an element given by his name.
	 * 
	 * @param name the name of the element.
	 * @param comment the comment to set.
	 */
	 public void setCommentByName(String name,String comment);
	
	/**
	 * Allows to get the comment on an element.
	 * 
	 * @param name the name of the element to get.
	 * @return a String corresponding to the comment of the element asked.
	 */
	 public String getCommentByName(String name);
	
	/** Allows to get the score of an element.
	 * 
	 * @param name the name of the element.
	 * @return a String representing the score of the Element.
	 */
	public String getScoreByName(String name);
	
	/**Allows to set the score of an element
	 * 
	 * @param name the name of the element.
	 * @param score a String representing the score to set.
	 */
	public void setScoreByName(String name,String score);
	
	/**
	 * Allows to get the name of all the authors.
	 * 
	 * @return a String that contains the names of all the authors.
	 */
	public String getAuthors();
	
	/**Allows to add an author to the list of authors.
	 * 
	 * @param nameString the name of the author to add.
	 * @param emailString the email of the author to add.
	 */
	public void addAuthor(String nameString, String emailString) ;

	/**Allows to delete an author.
	 * 
	 * @param name the name of the author to delete.
	 */
	public void delAuthor(String name);

	/* All the method describes backwards allow to get or set the element inside the header */
	
	
	/**Allows to get the application name.
	 * 
	 * @return a string corresponding to the application name.
	 */
	public String getAppname();
	
	/**Allows to set the application name.
	 * 
	 * 
	 * @param appname the application name to set.
	 */
	public void setAppname(String appname);
	
	
	/**Allows to get the language.
	 * 
	 * @return a string corresponding to the language.
	 */
	public String getLanguage();
	
	/**Allows to set the language.
	 * 
	 * 
	 * @param language the language to set.
	 */

	public void setLanguage(String language);

	/**Allows to get the release number.
	 * 
	 * @return a string corresponding to the release number.
	 */
	public String getRelease();
	
	/**Allows to set the release.
	 * 
	 * 
	 * @param release the release to set.
	 */

	public void setRelease(String release);
	
	/** Not implemented yet 
	 */
	public String getLicenselist();

	
	/**Allows to get the license Id.
	 * 
	 * @return a string corresponding to the License Id.
	 */
	public String getLicenseId();
	
	/**Allows to set the license Id.
	 * 
	 * 
	 * @param licenseId the license Id to set.
	 */
	public void setLicenseId(String licenseId);
	
	/**Allows to get the license Description.
	 * 
	 * @return a string corresponding to the license Description.
	 */	
	public String getLicenseDesc();
	
	/**Allows to set the license Description.
	 * 
	 * 
	 * @param licensedesc the license Description to set.
	 */

	public void setLicenseDesc(String licensedesc);
	
	/**Allows to get the url.
	 * 
	 * @return a string corresponding to the url.
	 */
	public String getUrl();
	
	/**Allows to set the url.
	 * 
	 * 
	 * @param url the url to set.
	 */
	public void setUrl(String url);
	
	
	/**Allows to get the description.
	 * 
	 * @return a string corresponding to the description.
	 */
	public String getDesc();
	
	/**Allows to set the description.
	 * 
	 * 
	 * @param desc the description to set.
	 */
	public void setDesc(String desc);
	
	/**Allows to get the demonstration url.
	 * 
	 * @return a string corresponding to the demonstration url.
	 */
	public String getDemoUrl();
	
	/**Allows to set the demonstration url.
	 * 
	 * 
	 * @param demourl the demonstration url to set.
	 */
	public void setDemoUrl(String demourl);
	
	/**Allows to get the QSOS format.
	 * 
	 * @return a string corresponding to the QSOS format.
	 */
	public String getQsosformat();
	
	/**Allows to set the QSOS format.
	 * 
	 * 
	 * @param qsosformat the QSOS format to set.
	 */
	public void setQsosformat(String qsosformat);
	
	/**Allows to get the QSOS specific format.
	 * 
	 * @return a string corresponding to the QSOS specific format.
	 */
	public String getQsosspecificformat();
	
	/**Allows to set the QSOS specific format.
	 * 
	 * 
	 * @param qsosspecificformat the QSOS specific format to set.
	 */

	public void setQsosspecificformat(String qsosspecificformat);
	
	/**Allows to get the application family in QSOS.
	 * 
	 * @return a string corresponding to the application family in QSOS.
	 */
	public String getQsosappfamily();
	
	/**Allows to set the application family in QSOS.
	 * 
	 * 
	 * @param qsosappfamily the application family in QSOS to set.
	 */

	public void setQsosappfamily(String qsosappfamily);

	/**Allows to write the xml file at the given path. 
	 * This method has a problem since it degrated the xml file (
	 * not the datas but the presentation).
	 * It will be fixed in the next version
	 * 
	 * @param path
	 */
	public void write(String path);

	/**
	 * 
	 */
	public List<SimpleMenuEntry> getSimpleTree();

	/**
	 * @param list
	 * @return
	 */
	public String Debugaffichage(List<SimpleMenuEntry> list);

	public ISheet getSheet();

	/**
	 * @param sheet
	 */
	public void setSheet(ISheet sheet);
}
