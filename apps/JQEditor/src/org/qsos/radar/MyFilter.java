/*
**  $Id: MyFilter.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
**
**  Copyright (C) 2006 ESME SUDRIA ( www.esme.fr ) 
**
**  Authors: 
**	BOUCHER Nicolas <shoub_n@hotmail.com>
**  	MODELIN Maxence  <maxence_modelin@hotmail.com>
**  	MULOT Louis <vindic@noos.fr>
**
**  This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
**  the Free Software Foundation; either version 2 of the License, or
**  (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
**
**
*/
package org.qsos.radar;

import org.eclipse.jface.viewers.Viewer;
import org.eclipse.jface.viewers.ViewerFilter;
import org.qsos.data.IElement;
import org.qsos.data.Messages;

/**
 * This class represents a filter that is used by the checkboxtree
 * 
 */
public class MyFilter extends ViewerFilter
{
	/**
	 * @param viewer
	 * 				Viewer
	 * @param element
	 * 				Object
	 * @param parentElement
	 * 				Object
	 * @return boolean
	 */
	public boolean select(Viewer viewer,Object parentElement,Object element)
	{	
		if (((IElement) element).getElements() == null)
		{	
			if (((IElement) element).getMeta().equalsIgnoreCase(Messages.getString("MyFilter.header")) || ((IElement) element).getScore() == null)   //Messages.getString("MyFilter.header") //$NON-NLS-1$
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}
}
