-- Add IMAGENS column to PRODUTO
ALTER TABLE "public"."PRODUTO" ADD COLUMN IF NOT EXISTS "IMAGENS" jsonb;

-- Create bucket
INSERT INTO storage.buckets (id, name, public) 
VALUES ('produtos-imagens', 'produtos-imagens', true)
ON CONFLICT (id) DO NOTHING;

-- Policy for public reading
CREATE POLICY "Public Access" 
ON storage.objects FOR SELECT 
USING ( bucket_id = 'produtos-imagens' );

-- Policy for authenticated users (or anon since we might be uploading via backend or anon key)
-- Since we are uploading from the backend or frontend with anon key, let's allow insert/update/delete
CREATE POLICY "Allow Uploads" 
ON storage.objects FOR INSERT 
WITH CHECK ( bucket_id = 'produtos-imagens' );

CREATE POLICY "Allow Updates" 
ON storage.objects FOR UPDATE 
USING ( bucket_id = 'produtos-imagens' );

CREATE POLICY "Allow Deletes" 
ON storage.objects FOR DELETE 
USING ( bucket_id = 'produtos-imagens' );
