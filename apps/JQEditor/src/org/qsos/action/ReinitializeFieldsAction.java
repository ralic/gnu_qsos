/*
**  $Id: ReinitializeFieldsAction.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
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
import org.qsos.interfaces.ReInitializeDialog;
import org.qsos.interfaces.SheetCTabItem;
import org.qsos.main.JQ;

/**
 * This class represents the Action to reinitialize a Qsos sheet 
 * 
 * 
 * <br>
 * first step:
 * <br>check the fields to reinitialize 
 * 
 * <br><br>second step:
 * <br>each line of the sheet is test to be reinitialize
 * 
 */

/**
 * @author MULOT_L
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class ReinitializeFieldsAction extends Action
{
	JQ window;
	
	/**
	 * @param w
	 */
	public ReinitializeFieldsAction(JQ w)
	{
		
		window = w;
		
		setText("Create@Ctrl+r"); //$NON-NLS-1$
		setToolTipText(Messages.getString("ReinitializeFieldsAction.toolTipTextReInitialize")); //$NON-NLS-1$
		
		setImageDescriptor( ImageDescriptor.createFromFile(null,"images/icons/reinitialize.png")); //$NON-NLS-1$
		//setImageDescriptor( ImageDescriptor.createFromURL(getClass().getResource("/images/icons/reinitialize.png")));
		
	}
	
	
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.action.IAction#run()
	 */
	public void run()
	{

		if (window.getCTabFolder().getItemCount() > 0)
		{
			

			ReInitializeDialog reInitializeDialog = new ReInitializeDialog(window.getShell());

		
			boolean[] answer = reInitializeDialog.run();
			
			if  (answer[0]||answer[1]||answer[2])
			{
				String answerUser = Messages.getString("ReinitializeFieldsAction.confirmationQuestion"); //$NON-NLS-1$
				if (answer[0])
				{
					answerUser = answerUser +"\n\t> Score";
				}
				if (answer[1])
				{
					answerUser = answerUser + "\n\t> Comment";
				}
				if (answer[2])
				{
					answerUser = answerUser + "\n\t> Header";
				}
					
				boolean areYouSure = MessageDialog.openQuestion(window.getShell(),Messages.getString("ReinitializeFieldsAction.confirmation"),answerUser); //$NON-NLS-1$
				if (areYouSure)
				{
					((SheetCTabItem)window.getCTabFolder().getSelection()).reInitialized(answer);
				}
			}
			else if (answer[3])
			{
				MessageDialog.openWarning(window.getShell(),Messages.getString("ReinitializeFieldsAction.error"),Messages.getString("ReinitializeFieldsAction.errorMessageSelectNothing")); //$NON-NLS-1$ //$NON-NLS-2$
			}
		}
		else
		{
			MessageDialog.openWarning(window.getShell(),Messages.getString("ReinitializeFieldsAction.error"),Messages.getString("ReinitializeFieldsAction.errorMessageNotOpenDoc")); //$NON-NLS-1$ //$NON-NLS-2$
		}
	}
}

