// Astro API Route - Proxies to Form Handler Worker
// Migrated from functions/api/contact.ts
export const prerender = false;

import type { APIRoute } from 'astro';

interface FileData {
  name: string;
  type: string;
  size: number;
  data: string; // base64 encoded
}

const FORM_HANDLER_URL = 'https://hercules-form-handler-uk-production.gilles-86d.workers.dev';
const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB max per file
const MAX_TOTAL_SIZE = 25 * 1024 * 1024; // 25MB max total

const corsHeaders = {
  'Access-Control-Allow-Origin': '*',
  'Access-Control-Allow-Methods': 'POST, OPTIONS',
  'Access-Control-Allow-Headers': 'Content-Type',
};

function arrayBufferToBase64(buffer: ArrayBuffer): string {
  const bytes = new Uint8Array(buffer);
  let binary = '';
  for (let i = 0; i < bytes.byteLength; i++) {
    binary += String.fromCharCode(bytes[i]);
  }
  return btoa(binary);
}

export const POST: APIRoute = async ({ request }) => {
  try {
    const formData = await request.formData();

    const files: FileData[] = [];
    let totalSize = 0;

    for (const [key, value] of formData.entries()) {
      if (key.startsWith('file_') && value instanceof File && value.size > 0) {
        if (value.size > MAX_FILE_SIZE) {
          return new Response(
            JSON.stringify({
              success: false,
              error: `File "${value.name}" is too large. Maximum size: 10MB`,
            }),
            { status: 400, headers: { 'Content-Type': 'application/json', ...corsHeaders } }
          );
        }

        totalSize += value.size;
        if (totalSize > MAX_TOTAL_SIZE) {
          return new Response(
            JSON.stringify({ success: false, error: 'Total file size exceeds 25MB' }),
            { status: 400, headers: { 'Content-Type': 'application/json', ...corsHeaders } }
          );
        }

        const arrayBuffer = await value.arrayBuffer();
        const base64Data = arrayBufferToBase64(arrayBuffer);

        files.push({
          name: value.name,
          type: value.type || 'application/octet-stream',
          size: value.size,
          data: base64Data,
        });
      }
    }

    const payload: Record<string, unknown> = {};
    for (const [key, value] of formData.entries()) {
      if (typeof value === 'string') {
        payload[key] = value;
      }
    }

    payload.ip = request.headers.get('CF-Connecting-IP') || request.headers.get('X-Forwarded-For') || '';
    payload.country = request.headers.get('CF-IPCountry') || '';
    payload.city = request.headers.get('CF-IPCity') || '';
    payload.region = request.headers.get('CF-IPRegion') || '';

    if (files.length > 0) {
      payload.uploadFiles = files;
    }

    const response = await fetch(`${FORM_HANDLER_URL}/contact`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    const result = await response.json();

    return new Response(JSON.stringify(result), {
      status: response.status,
      headers: { 'Content-Type': 'application/json', ...corsHeaders },
    });
  } catch (error) {
    console.error('Contact form proxy error:', error);
    return new Response(
      JSON.stringify({
        success: false,
        error: error instanceof Error ? error.message : 'Unknown error',
      }),
      { status: 500, headers: { 'Content-Type': 'application/json', ...corsHeaders } }
    );
  }
};

export const OPTIONS: APIRoute = async () => {
  return new Response(null, { status: 204, headers: corsHeaders });
};
