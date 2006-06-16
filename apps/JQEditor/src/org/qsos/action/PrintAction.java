/*
**  $Id: PrintAction.java,v 1.1 2006/06/16 14:16:35 goneri Exp $
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
import org.eclipse.swt.graphics.GC;
import org.eclipse.swt.graphics.Point;
import org.eclipse.swt.graphics.Rectangle;
import org.eclipse.swt.printing.PrintDialog;
import org.eclipse.swt.printing.Printer;
import org.eclipse.swt.printing.PrinterData;
import org.qsos.data.IElement;
import org.qsos.data.Messages;
import org.qsos.interfaces.SheetCTabItem;
import org.qsos.main.JQ;

/**
 * This class represents the Action to print a Qsos sheet on a paper
 * 
 * 
 * <br>
 * first step:
 * <br>open print dialog box to choose the printer
 * 
 * <br><br>second step:
 * <br>Start the printing job
 * 
 * <br><br>Third step:
 * <br>find the better render
 */

/**
 * @author MULOT_L
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class PrintAction extends Action
{
	JQ window;
	
	/**
	 * @param w
	 */
	public PrintAction(JQ w)
	{
		
		window = w;
		
		setText("Create@Ctrl+p"); //$NON-NLS-1$
		setToolTipText(Messages.getString("PrintAction.toolTipTextPrint")); //$NON-NLS-1$
		setImageDescriptor( ImageDescriptor.createFromFile(null,"images/icons/printer.png")); //$NON-NLS-1$
	}
	
	/* (non-Javadoc)
	 * @see org.eclipse.jface.action.IAction#run()
	 */
	public void run()
	{
		
		final PrintDialog print_dialog = new PrintDialog(window.getShell());
	
		window.getCTabFolder().getItemCount();
		
		if(window.getCTabFolder().getItemCount() != 0)
		{
			// Open the printer dialog box
			PrinterData printerData = print_dialog.open();
			
			if (printerData != null)
			{
				// Get the characters of the sheet to print
				String text=((IElement)((SheetCTabItem)window.getCTabFolder().getSelection()).getSaveLibQSOS().getSheet().getRoot()).tree();	
				
				if (print_dialog != null) 
				{
					// Create the printer
					Printer printer = new Printer(printerData);
					String fileName="Qsos report";  //$NON-NLS-1$
					
					// Print the contents of the file
					new WrappingPrinter(printer, fileName, text).print();
					
					// Dispose the printer
					printer.dispose();
				}
			}
		}
		else
		{
			MessageDialog.openWarning(window.getShell(),Messages.getString("PrintAction.error"),Messages.getString("PrintAction.errorMessage")); //$NON-NLS-1$ //$NON-NLS-2$
		}
	}
}

/**
 * @author MULOT_L
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
class WrappingPrinter 
{
	private Printer printer; // The printer
	private String fileName; // The name of the file to print
	private GC gc; // The GC to print on
	private String contents; // The contents of the file to print
	private int xPos, yPos; // The current x and y locations for print
	private Rectangle bounds; // The boundaries for the print
	private StringBuffer buf; // Holds a word at a time
	private int lineHeight; // The height of a line of text
	
	/**
	 * WrappingPrinter constructor
	 * @param printer the printer
	 * @param fileName the fileName
	 * @param text the contents
	 */
	
	// Constructor of the class
	WrappingPrinter(Printer printer, String fileName, String text) 
	{
		this.printer = printer;
		this.fileName = fileName;
		this.contents = text;
	}
	
	/**
	 * Method print()
	 * 
	 * Start the print job and perform the format
	 * 
	 */
	void print() 
	{
		// Start the print job
		if (printer.startJob(fileName)) 
		{
			// Determine print area, with margins
			bounds = computePrintArea(printer);
			xPos = bounds.x;
			yPos = bounds.y;
			
			// Create the GC
			gc = new GC(printer);
			
			// Determine line height
			lineHeight = gc.getFontMetrics().getHeight();
			
			// Determine tab width--use three spaces for tabs
			int tabWidth = gc.stringExtent("   ").x; //$NON-NLS-1$
			
			// Print the text
			printer.startPage();
			buf = new StringBuffer();
			char c;
			for (int i = 0, n = contents.length(); i < n; i++) 
			{
				// Get the next character
				c = contents.charAt(i);
				
				// Check for newline
				if (c == '\n') 
				{
					printBuffer();
					printNewline();
				}
				// Check for tab
				else if (c == '\t') 
				{
					xPos += tabWidth;
				}
				else 
				{
					buf.append(c);
					// Check for space
					if (Character.isWhitespace(c)) 
					{
						printBuffer();
					}
				}
			}
			printer.endPage();
			printer.endJob();
			gc.dispose();
		}
	}
	
	/**
	 * Method printBuffer()
	 * 
	 * Buffer allow good presentation of the final render
	 * 
	 */
	void printBuffer() 
	{
		// Get the width of the rendered buffer
		int width = gc.stringExtent(buf.toString()).x;
		
		// Determine if it fits
		if (xPos + width > bounds.x + bounds.width) 
		{
			// Doesn't fit--wrap
			printNewline();
		} 
		// Print the buffer
		gc.drawString(buf.toString(), xPos, yPos, false);
		xPos += width;
		buf.setLength(0);
	}
	
	/**
	 * Method printNewliner()
	 * 
	 * Prints a newline
	 */
	void printNewline() 
	{
		// Reset x and y locations to next line
		xPos = bounds.x;
		yPos += lineHeight;
		
		// Have we gone to the next page?
		if (yPos > bounds.y + bounds.height) 
		{
			yPos = bounds.y;
			printer.endPage();
			printer.startPage();
		}
	}
	
	/**
	 * @param printer
	 * @return rectangle
	 */
	Rectangle computePrintArea(Printer printer) 
	{
		// Get the printable area
		Rectangle rect = printer.getClientArea();
		
		// Compute the trim
		Rectangle trim = printer.computeTrim(0, 0, 0, 0);
		
		// Get the printer's DPI
		Point dpi = printer.getDPI();
		
		// Calculate the printable area, using 1 inch margins
		int left = trim.x + dpi.x;
		if (left < rect.x) left = rect.x;
		
		int right = (rect.width + trim.x + trim.width) - dpi.x;
		if (right > rect.width) right = rect.width;
		
		int top = trim.y + dpi.y;
		if (top < rect.y) top = rect.y;
		
		int bottom = (rect.height + trim.y + trim.height) - dpi.y;
		if (bottom > rect.height) bottom = rect.height;
		
		return new Rectangle(left, top, right - left, bottom - top);
	}
};
