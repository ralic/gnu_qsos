/*
**  $Id: ImageRegistryQSOS.java,v 1.2 2006/06/16 14:49:57 goneri Exp $
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
/**
 * 
 */
package org.qsos.data;


import java.io.FileInputStream;
import java.io.FileNotFoundException;

import org.eclipse.jface.resource.ImageRegistry;
import org.eclipse.swt.graphics.Image;



/**
 * @author MULOT_L
 *
 * This class must gather the image of the JQ application.
 * Some problem exist again.
 * 
 * TODO fix error on this class
 */
public class ImageRegistryQSOS extends ImageRegistry
{
	
	/**
	 * 
	 */
	public ImageRegistryQSOS ()
	{		
		
		super();
		createImageRegistryQSOS(this);
	}
	
	/**
	 * @param iR
	 */
	protected void createImageRegistryQSOS(ImageRegistryQSOS iR)
	{
		Image image_qsos = null;
		Image icon_penguin_alone_QSOS_16 = null;
		
		try 
		{
			image_qsos = new Image( null, new FileInputStream("share/image_QSOS_200.png"));
		} catch (FileNotFoundException e) 
		{
			System.out.println("Error loading: share/qsos.gif");
		}
		
		
		try 
		{
			icon_penguin_alone_QSOS_16 = new Image( null, new FileInputStream("share/icons/penguin_alone.bmp"));
		} catch (FileNotFoundException e) 
		{
			System.out.println("Error loading: src/share/icons/penguin_alone.bmp");
		}
		
		

		iR.put("image_qsos",image_qsos);
		iR.put("icon_penguin_alone_QSOS_16",icon_penguin_alone_QSOS_16);

		
	}	
	
	
}



