/* $Id: IDesXMLizer.java,v 1.1 2006/04/07 12:03:39 aclerf Exp $
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

package com.ao.o3s.transformation;

import java.io.IOException;
import java.net.URL;

import org.jdom.JDOMException;

import com.ao.o3s.data.ISheet;

/**
 * This interface should be implemented by 
 * a DesXMLizer.
 * 
 * @author aclerf
 *
 */

public interface IDesXMLizer {
	
	/**
	 * <p> This method transforms an xml file given by is url
	 * (use "file:///path/toto.xml" if local) and returns an 
	 * IElement which is the root element of the xml file 
	 * in the java modelisation.  
	 * @exception JDOMException
	 * @param url the url of the xml file
	 * @return ISheet the sheet in the java modelisation
	 */
	public ISheet transformFromXML(URL url) throws JDOMException, IOException;
}
