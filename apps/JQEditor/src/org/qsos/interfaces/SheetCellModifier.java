/*
**  $Id: SheetCellModifier.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
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
package org.qsos.interfaces;

import java.io.IOException;
import org.eclipse.jface.viewers.*;
import org.eclipse.swt.widgets.Item;
import org.qsos.data.IElement;
import org.qsos.data.JQConst;
import org.qsos.data.Messages;

/**
 * This class represents the cell modifier for the JQEditor program
 */
/**
 * @author MULOT_L
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class SheetCellModifier implements ICellModifier
{
	private Viewer	viewer;
	private String title;
	private String desc;
	private String comment;
	private String score;

	/**
	 * @param viewer
	 */
	public SheetCellModifier(Viewer viewer)
	{
		this.viewer = viewer;
		title = Messages.getString("JQ.TitleColumn");
		desc = Messages.getString("JQ.DescColumn");
		comment =  Messages.getString("JQ.CommentColumn");
		score = Messages.getString("JQ.ScoreColumn");
	}

	/**
	 * Returns whether the property can be modified
	 * 
	 * @param element
	 *            the element
	 * @param property
	 *            the property
	 * @return boolean
	 */
	/* (non-Javadoc)
	 * @see org.eclipse.jface.viewers.ICellModifier#canModify(java.lang.Object, java.lang.String)
	 */
	public boolean canModify(Object element, String property)
	{
		// Allow editing of all values
		
		// Can be changed by the last condition more the condition on the property
		if ( ((String) ((IElement)element).getDesc0() != "" ) && ((String) ((IElement)element).getDesc1() != "" ) && ((String) ((IElement)element).getDesc2() != "" )) //$NON-NLS-1$ //$NON-NLS-2$ //$NON-NLS-3$
		{
			if ( property.equalsIgnoreCase(score))
			{
				//viewer.getControl().setBackground(new Color(null, 1,100,1));
				return true;
			}
			else if ( property.equalsIgnoreCase(comment))
			{
				//viewer.getControl().setBackground(new Color(null, 1,100,1));
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ( property.equalsIgnoreCase(comment))
			{
				//viewer.getControl().setBackground(new Color(null, 1,100,1));
				return true;
			}
			else
			{
				return false;
			}
		}
		
		//return true;
	}

	
	/**
	 * Returns the value for the property
	 * 
	 * @param element
	 *            the element
	 * @param property
	 *            the property
	 * @return Object
	 */
	public Object getValue(Object element, String property)
	{
		IElement p = (IElement) element;
		if (title.equals(property))
		{
			return p.getTitle();
		}
		else if (desc.equalsIgnoreCase(property))
		{
			if (( (IElement) element).getDesc() != "") //$NON-NLS-1$
			{
				return p.getDesc();
			}
			else// if ( ((IElement) element).getScore() != "" )
			{
				if (((IElement) element).getScore() == JQConst.SCORE_0)
				{
					return p.getDesc0();
				}
				else if (((IElement) element).getScore() == JQConst.SCORE_1)
				{
					return p.getDesc1();
				}
				else
				{
					return p.getDesc2();
				}
			}
		}
		else if (comment.equalsIgnoreCase(property))
		{
			return p.getComment();
		}
		else if (score.equalsIgnoreCase(property))
		{
			return p.getScore();
		}
		else
		{
			return null;
		}
	
	}

	/**
	 * Modifies the element
	 * 
	 * @param element
	 *            the element
	 * @param property
	 *            the property
	 * @param value
	 *            the value
	 */
	public void modify(Object element, String property, Object value)
	{
		if (element instanceof Item)
		{
			element = ((Item) element).getData();
		}

		
		IElement p = (IElement) element;
		
		if (title.equals(property))
		{
			p.setTitle((String) value);
		}
		else if (desc.equalsIgnoreCase(property))
		{
			if (( (IElement) element).getDesc() != "") //$NON-NLS-1$
			{
				p.setDesc((String) value);
			}
			else// if ( ((IElement) element).getScore() != "" )
			{
				if (((IElement) element).getScore() == JQConst.SCORE_0)
				{
					p.setDesc0((String) value);
				}
				else if (((IElement) element).getScore() == JQConst.SCORE_1)
				{
					p.setDesc1((String) value);
				}
				else
				{
					p.setDesc2((String) value);
				}
			}
		}
		else if (comment.equalsIgnoreCase(property))
		{
			p.setComment((String) value);
		}
		else if (score.equalsIgnoreCase(property))
		{
			try
			{
				// If the value is not 0,1 or 2 the value is not save
				if ((((String)value).equals(JQConst.SCORE_0))||(((String)value).equals(JQConst.SCORE_1))||(((String)value).equals(JQConst.SCORE_2)))
				{
					p.setScore((String) value);
				}
			} catch (IOException e)
			{
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}

		// Force the viewer to refresh
		viewer.refresh();
	}
}