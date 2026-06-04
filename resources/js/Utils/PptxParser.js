/**
 * PPTX Parser Utility
 * Parse PowerPoint files using jszip and extract slide content
 */

import JSZip from 'jszip';

export class PptxParser {
  constructor() {
    this.zip = null;
    this.slides = [];
    this.slideCount = 0;
  }

  /**
   * Parse PPTX file from ArrayBuffer
   * First try to extract slide thumbnail images, fallback to rendering
   */
  async parse(arrayBuffer) {
    try {
      this.zip = new JSZip();
      await this.zip.loadAsync(arrayBuffer);

      // Get slide count
      const presentation = await this.getPresentation();
      this.slideCount = presentation.slideCount || 0;

      // Parse each slide
      this.slides = [];
      for (let i = 1; i <= this.slideCount; i++) {
        try {
          // First try to extract slide image (thumbnail)
          const slideImage = await this.extractSlideImage(i);
          if (slideImage) {
            this.slides.push({
              number: i,
              backgroundColor: '#FFFFFF',
              shapes: [],
              width: 960,
              height: 540,
              imageUrl: slideImage, // Pre-extracted image
              isExtractedImage: true
            });
          } else {
            // Fallback: parse and render from XML
            const slide = await this.parseSlide(i);
            this.slides.push(slide);
          }
        } catch (err) {
          console.warn(`Error parsing slide ${i}:`, err);
          // Continue with next slide
        }
      }

      return {
        slideCount: this.slideCount,
        slides: this.slides
      };
    } catch (err) {
      console.error('Error parsing PPTX:', err);
      throw new Error(`Failed to parse PPTX: ${err.message}`);
    }
  }

  /**
   * Try to extract pre-rendered slide image from PPTX
   * PowerPoint stores thumbnails in ppt/slides/_rels/slide{N}.xml.rels
   */
  async extractSlideImage(slideNum) {
    try {
      // Fallback: check for slide thumbnails in ppt/slideThumbnails/
      const thumbnailPath = `ppt/slideThumbnails/thumbnail${slideNum}.jpeg`;
      const thumbnailFile = this.zip.file(thumbnailPath);
      
      if (thumbnailFile) {
        const thumbnailData = await thumbnailFile.async('arraybuffer');
        const blob = new Blob([thumbnailData], { type: 'image/jpeg' });
        const url = URL.createObjectURL(blob);
        console.log(`Extracted thumbnail for slide ${slideNum}`);
        return url;
      }

      // Also try PNG format
      const thumbnailPngPath = `ppt/slideThumbnails/thumbnail${slideNum}.png`;
      const thumbnailPngFile = this.zip.file(thumbnailPngPath);
      
      if (thumbnailPngFile) {
        const thumbnailData = await thumbnailPngFile.async('arraybuffer');
        const blob = new Blob([thumbnailData], { type: 'image/png' });
        const url = URL.createObjectURL(blob);
        console.log(`Extracted PNG thumbnail for slide ${slideNum}`);
        return url;
      }

      return null;
    } catch (err) {
      console.warn(`Could not extract slide image for slide ${slideNum}:`, err);
      return null;
    }
  }

  /**
   * Get presentation.xml data
   */
  async getPresentation() {
    try {
      const presentationXml = await this.zip.file('ppt/presentation.xml').async('text');
      const parser = new DOMParser();
      const xmlDoc = parser.parseFromString(presentationXml, 'text/xml');

      // Count slides from presentation.xml
      const sldIdLst = xmlDoc.querySelector('p\\:sldIdLst, sldIdLst');
      const slideCount = sldIdLst ? sldIdLst.children.length : 0;

      return { slideCount };
    } catch (err) {
      console.warn('Error reading presentation.xml:', err);
      return { slideCount: 0 };
    }
  }

  /**
   * Parse individual slide
   */
  async parseSlide(slideNum) {
    const slidePath = `ppt/slides/slide${slideNum}.xml`;
    const slideFile = this.zip.file(slidePath);

    if (!slideFile) {
      throw new Error(`Slide ${slideNum} not found`);
    }

    const slideXml = await slideFile.async('text');
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(slideXml, 'text/xml');

    // Extract background color
    const bgColor = this.extractBackgroundColor(xmlDoc);

    // Extract shapes and text
    const shapes = this.extractShapes(xmlDoc);

    return {
      number: slideNum,
      backgroundColor: bgColor,
      shapes: shapes,
      width: 960,
      height: 540
    };
  }

  /**
   * Extract background color from slide
   */
  extractBackgroundColor(xmlDoc) {
    try {
      // Try to find solid fill color
      const solidFill = xmlDoc.querySelector('p\\:cSld p\\:bg a\\:solidFill > a\\:srgbClr, cSld bg solidFill srgbClr');
      if (solidFill) {
        return '#' + solidFill.getAttribute('val');
      }

      // Default white background
      return '#FFFFFF';
    } catch (err) {
      return '#FFFFFF';
    }
  }

  /**
   * Extract all shapes (text, rectangles, circles, etc) from slide
   */
  extractShapes(xmlDoc) {
    const shapes = [];
    
    try {
      // Get all shape elements
      const shapeElements = xmlDoc.querySelectorAll('p\\:sp, sp');

      shapeElements.forEach((shape, idx) => {
        try {
          const shapeData = this.parseShape(shape, idx);
          if (shapeData) {
            shapes.push(shapeData);
          }
        } catch (err) {
          console.warn('Error parsing shape:', err);
        }
      });
    } catch (err) {
      console.warn('Error extracting shapes:', err);
    }

    return shapes;
  }

  /**
   * Parse individual shape with enhanced details
   */
  parseShape(shapeElement, index) {
    try {
      // Get position and size
      const xfrm = shapeElement.querySelector('p\\:spPr a\\:xfrm, spPr xfrm');
      const position = this.extractPosition(xfrm);

      // Get shape type/name
      const nvSpPr = shapeElement.querySelector('p\\:nvSpPr, nvSpPr');
      const shapeName = nvSpPr?.querySelector('p\\:cNvPr, cNvPr')?.getAttribute('name') || `Shape ${index}`;

      // Get text content with styling
      const textBody = shapeElement.querySelector('p\\:txBody, txBody');
      const textData = this.extractTextWithStyling(textBody);

      // Get fill color
      const color = this.extractShapeColor(shapeElement);

      return {
        id: index,
        name: shapeName,
        type: 'shape',
        text: textData.text,
        textColor: textData.color,
        fontSize: textData.fontSize,
        fontBold: textData.bold,
        color: color,
        ...position
      };
    } catch (err) {
      console.warn('Error parsing shape element:', err);
      return null;
    }
  }

  /**
   * Extract text with styling information
   */
  extractTextWithStyling(textBody) {
    if (!textBody) return { text: '', color: '#000000', fontSize: 14, bold: false };

    try {
      const paragraphs = textBody.querySelectorAll('a\\:p, p');
      const texts = [];
      let primaryColor = '#000000';
      let primaryFontSize = 14;
      let primaryBold = false;

      paragraphs.forEach((para, paraIdx) => {
        const runs = para.querySelectorAll('a\\:r, r');
        let paraText = '';

        runs.forEach((run, runIdx) => {
          const t = run.querySelector('a\\:t, t');
          if (t && t.textContent) {
            paraText += t.textContent;

            // Extract styling from first run
            if (paraIdx === 0 && runIdx === 0) {
              // Get font size
              const rPr = run.querySelector('a\\:rPr, rPr');
              if (rPr) {
                const sz = rPr.getAttribute('sz');
                if (sz) {
                  primaryFontSize = Math.round(parseInt(sz) / 100); // Convert from 1/100th of a point
                }

                // Get bold
                primaryBold = rPr.getAttribute('b') === '1' || rPr.getAttribute('b') === 'true';

                // Get color
                const solidFill = rPr.querySelector('a\\:solidFill, solidFill');
                if (solidFill) {
                  const srgbClr = solidFill.querySelector('a\\:srgbClr, srgbClr');
                  if (srgbClr) {
                    primaryColor = '#' + srgbClr.getAttribute('val');
                  }
                }
              }
            }
          }
        });

        if (paraText) {
          texts.push(paraText);
        }
      });

      return {
        text: texts.join('\n').trim(),
        color: primaryColor,
        fontSize: primaryFontSize,
        bold: primaryBold
      };
    } catch (err) {
      return { text: '', color: '#000000', fontSize: 14, bold: false };
    }
  }

  /**
   * Extract position and size from transform
   * Standard slide in PPTX: 10 inches (9144000 EMU) × 7.5 inches (6858000 EMU)
   * Canvas target: 960 × 540 pixels
   */
  extractPosition(xfrmElement) {
    try {
      const off = xfrmElement?.querySelector('a\\:off, off');
      const ext = xfrmElement?.querySelector('a\\:ext, ext');

      // Standard PPTX slide dimensions in EMU
      const SLIDE_WIDTH_EMU = 9144000;    // 10 inches
      const SLIDE_HEIGHT_EMU = 6858000;   // 7.5 inches
      const CANVAS_WIDTH = 960;
      const CANVAS_HEIGHT = 540;

      // Get EMU values
      const x_emu = parseInt(off?.getAttribute('x') || 0);
      const y_emu = parseInt(off?.getAttribute('y') || 0);
      const width_emu = parseInt(ext?.getAttribute('cx') || 100000);
      const height_emu = parseInt(ext?.getAttribute('cy') || 100000);

      // Convert EMU to canvas pixels
      const x = (x_emu / SLIDE_WIDTH_EMU) * CANVAS_WIDTH;
      const y = (y_emu / SLIDE_HEIGHT_EMU) * CANVAS_HEIGHT;
      const width = (width_emu / SLIDE_WIDTH_EMU) * CANVAS_WIDTH;
      const height = (height_emu / SLIDE_HEIGHT_EMU) * CANVAS_HEIGHT;

      return {
        x: Math.max(0, x),
        y: Math.max(0, y),
        width: Math.max(1, width),
        height: Math.max(1, height)
      };
    } catch (err) {
      return { x: 0, y: 0, width: 100, height: 50 };
    }
  }

  /**
   * Extract text from shape
   */
  extractText(textBody) {
    if (!textBody) return '';

    try {
      const paragraphs = textBody.querySelectorAll('a\\:p, p');
      const texts = [];

      paragraphs.forEach(para => {
        const runs = para.querySelectorAll('a\\:r, r');
        let paraText = '';

        runs.forEach(run => {
          const t = run.querySelector('a\\:t, t');
          if (t && t.textContent) {
            paraText += t.textContent;
          }
        });

        if (paraText) {
          texts.push(paraText);
        }
      });

      return texts.join('\n').trim();
    } catch (err) {
      return '';
    }
  }

  /**
   * Extract shape fill color with better fallbacks
   */
  extractShapeColor(shapeElement) {
    try {
      const spPr = shapeElement.querySelector('p\\:spPr, spPr');
      const solidFill = spPr?.querySelector('a\\:solidFill, solidFill');
      
      if (solidFill) {
        // Try RGB color
        const srgbClr = solidFill.querySelector('a\\:srgbClr, srgbClr');
        if (srgbClr) {
          return '#' + srgbClr.getAttribute('val');
        }

        // Try scheme color with alpha fallback
        const schemeClr = solidFill.querySelector('a\\:schemeClr, schemeClr');
        if (schemeClr) {
          const schemeName = schemeClr.getAttribute('val');
          // Map common scheme colors
          const schemeMap = {
            'accent1': '#4472C4',
            'accent2': '#ED7D31',
            'accent3': '#A5A5A5',
            'accent4': '#FFC000',
            'accent5': '#5B9BD5',
            'accent6': '#70AD47'
          };
          return schemeMap[schemeName] || '#CCCCCC';
        }
      }

      // Check for line properties (stroke color)
      const ln = spPr?.querySelector('a\\:ln, ln');
      if (ln) {
        const lnSolidFill = ln.querySelector('a\\:solidFill, solidFill');
        if (lnSolidFill) {
          const srgbClr = lnSolidFill.querySelector('a\\:srgbClr, srgbClr');
          if (srgbClr) {
            return '#' + srgbClr.getAttribute('val');
          }
        }
      }

      return '#CCCCCC';
    } catch (err) {
      return '#CCCCCC';
    }
  }

  /**
   * Render slide to canvas (or return extracted image if available)
   */
  renderSlideToCanvas(slide, canvas, canvasWidth = 960, canvasHeight = 540) {
    // If this slide has pre-extracted high-quality image, use that instead
    if (slide.isExtractedImage && slide.imageUrl) {
      return slide.imageUrl;
    }

    const ctx = canvas.getContext('2d');
    canvas.width = canvasWidth;
    canvas.height = canvasHeight;

    // Fill background
    ctx.fillStyle = slide.backgroundColor || '#FFFFFF';
    ctx.fillRect(0, 0, canvasWidth, canvasHeight);

    // Render each shape
    slide.shapes.forEach(shape => {
      this.renderShape(ctx, shape, canvasWidth, canvasHeight);
    });

    return canvas.toDataURL('image/png');
  }

  /**
   * Render individual shape
   */
  renderShape(ctx, shape, canvasWidth, canvasHeight) {
    try {
      // Convert positions to canvas coordinates
      const scaleX = canvasWidth / 960;
      const scaleY = canvasHeight / 540;

      const x = shape.x * scaleX;
      const y = shape.y * scaleY;
      const width = shape.width * scaleX;
      const height = shape.height * scaleY;

      // Draw text
      if (shape.text) {
        this.renderText(ctx, shape, x, y, width, height);
      } else {
        // Draw shape background
        ctx.fillStyle = shape.color || '#CCCCCC';
        ctx.fillRect(x, y, width, height);
        ctx.strokeStyle = '#999999';
        ctx.lineWidth = 1;
        ctx.strokeRect(x, y, width, height);
      }
    } catch (err) {
      console.warn('Error rendering shape:', err);
    }
  }

  /**
   * Render text in shape with proper styling
   */
  renderText(ctx, shape, x, y, width, height) {
    try {
      const fontSize = shape.fontSize || 14;
      const color = shape.textColor || '#000000';
      const fontBold = shape.fontBold ? 'bold ' : '';
      
      ctx.fillStyle = color;
      ctx.font = `${fontBold}${fontSize}px Arial, sans-serif`;
      ctx.textBaseline = 'top';
      ctx.textAlign = 'left';

      const lines = shape.text.split('\n');
      let currentY = y + 8;
      const lineHeight = fontSize + 4;

      lines.forEach(line => {
        if (currentY < y + height - 8) {
          // Wrap long text
          const wrappedLines = this.wrapText(ctx, line, width - 16);
          wrappedLines.forEach(wrappedLine => {
            if (currentY < y + height - 8) {
              ctx.fillText(wrappedLine, x + 8, currentY);
              currentY += lineHeight;
            }
          });
        }
      });
    } catch (err) {
      console.warn('Error rendering text:', err);
    }
  }

  /**
   * Wrap text to fit width
   */
  wrapText(ctx, text, maxWidth) {
    const words = text.split(' ');
    const lines = [];
    let currentLine = '';

    words.forEach(word => {
      const testLine = currentLine + (currentLine ? ' ' : '') + word;
      const metrics = ctx.measureText(testLine);

      if (metrics.width > maxWidth && currentLine) {
        lines.push(currentLine);
        currentLine = word;
      } else {
        currentLine = testLine;
      }
    });

    if (currentLine) {
      lines.push(currentLine);
    }

    return lines;
  }
}
