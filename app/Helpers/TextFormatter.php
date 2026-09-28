<?php

namespace App\Helpers;

class TextFormatter
{
    /**
     * Format description text into bullet points or paragraphs.
     * If the text contains lines starting with bullet markers (-, *, •),
     * it will format them as a <ul> list.
     * Otherwise, it will preserve line breaks.
     */
    public static function format($text)
    {
        if (empty($text)) {
            return '';
        }

        // Standardize line breaks and split
        $lines = explode("\n", str_replace("\r", "", $text));
        
        $isListActive = false;
        $html = '';

        foreach ($lines as $line) {
            $trimmed = trim($line);
            
            if ($trimmed === '') {
                if ($isListActive) {
                    $html .= '</ul>';
                    $isListActive = false;
                }
                continue;
            }

            // Match bullet point markers: -, *, •, +, or lists
            if (preg_match('/^[\-\*\•\+]\s*(.*)$/u', $trimmed, $matches)) {
                if (!$isListActive) {
                    $html .= '<ul class="bullet-list">';
                    $isListActive = true;
                }
                $html .= '<li>' . e($matches[1]) . '</li>';
            } else {
                if ($isListActive) {
                    $html .= '</ul>';
                    $isListActive = false;
                }
                $html .= '<p>' . nl2br(e($line)) . '</p>';
            }
        }

        if ($isListActive) {
            $html .= '</ul>';
        }

        return $html;
    }
}
