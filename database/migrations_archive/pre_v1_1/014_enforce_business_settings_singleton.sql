ALTER TABLE business_settings 
    ADD CONSTRAINT chk_business_settings_singleton 
    CHECK (id = 1);