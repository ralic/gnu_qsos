/* $Id: IXMLizer.java,v 1.2 2006/04/12 10:15:15 aclerf Exp $
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
package org.qsos.transformation;

import org.jdom.Document;
import org.qsos.data.ISheet;

/**
 * <p> This interface should be implemented
 * by the class JDomImpl. It allows to convert
 * the modelisation in java into a String which contains
 * the xml.
 * 
 * @author Romain Pelisse <romain.pelisse@atosorigin.com>
 *
 */
public interface IXMLizer 
{
	/**
	 * This method takes an ISheet, which contains the root element of 
	 * the java modelisation and returns a String representation
	 * of the xml
	 * 
	 *  @param sheet the ISheet in the java modelisation.
	 *  @return String the string representation of the xml.
	 * 
	 */
	public String transformToXml(ISheet sheet);
	
	/**
	 * This method takes an ISheet, which contains the root element of 
	 * the java modelisation and create the xml file given
	 * by the path 
	 * 
	 *  @param sheet the ISheet in the java modelisation.
	 *  @param path the path of the file to create.
	 * 
	 */	
	public void transformToXml(ISheet sheet,String path);
	
	/**
	 * This method converts a document (org.jdom.Document)
	 * into a String.
	 * 
	 *  @param doc the document to convert.
	 *  @return String the string representation of the doc.
	 * 
	 */
	public String docToString(Document doc);   
	
}


