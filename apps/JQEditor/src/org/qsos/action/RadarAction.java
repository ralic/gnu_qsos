/*
**  $Id: RadarAction.java,v 1.2 2006/06/16 14:49:57 goneri Exp $
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
package org.qsos.action;


import org.eclipse.jface.action.Action;
import org.eclipse.jface.dialogs.MessageDialog;
import org.eclipse.jface.resource.ImageDescriptor;
import org.qsos.data.Messages;
import org.qsos.interfaces.RadarDialog;
import org.qsos.main.JQ;

/**
 * @author MODELIN
 *
 * This class represents the Action to open the radar dialog box
 * 
 */
public class RadarAction extends Action
{
	JQ window;
	
	/**
	 * @param w
	 */
	public RadarAction(JQ w)
	{
		window = w;
		
		setText("specialDisplay@Ctrl+d"); //$NON-NLS-1$
		setToolTipText(Messages.getString("RadarAction.toolTipTextRadar")); //$NON-NLS-1$
		setImageDescriptor( ImageDescriptor.createFromFile(null,"share/icons/special_display.png")); //$NON-NLS-1$
	}
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.action.IAction#run()
	 */
	public void run()
	{
		if ( window.getCTabFolder().getItemCount() > 0)
		{					
			RadarDialog radar = new RadarDialog(window.getShell(),window);
			radar.run();
		}	
		else
		{
			MessageDialog.openWarning(window.getShell(),Messages.getString("RadarAction.errorRadar"),Messages.getString("RadarAction.errorMessageRadar")); //$NON-NLS-1$ //$NON-NLS-2$
			
		}
	}
}

