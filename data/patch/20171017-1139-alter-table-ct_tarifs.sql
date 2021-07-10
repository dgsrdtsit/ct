TRUNCATE ct_usage_tarif;
TRUNCATE ct_genre_tarif;
TRUNCATE ct_motif_tarif;
ALTER TABLE ct_genre_tarif CHANGE grt_annee grt_annee VARCHAR(4) DEFAULT NULL;
CREATE UNIQUE INDEX uk_ct_genre_ct_annee ON ct_genre_tarif (grt_annee, ct_genre_id);
ALTER TABLE ct_motif_tarif CHANGE mtf_trf_date mtf_trf_date VARCHAR(4) DEFAULT NULL;
CREATE UNIQUE INDEX uk_ct_motif_ct_mtf_trf_date ON ct_motif_tarif (mtf_trf_date, ct_motif_id);
ALTER TABLE ct_usage_tarif CHANGE usg_trf_annee usg_trf_annee VARCHAR(4) DEFAULT NULL;
CREATE UNIQUE INDEX uk_ct_usage_ct_usg_trf_annee ON ct_usage_tarif (usg_trf_annee, ct_usage_id);
ALTER TABLE ct_visite_extra_tarif CHANGE vet_annee vet_annee VARCHAR(4) DEFAULT NULL;
CREATE UNIQUE INDEX uk_ct_visite_extra_ct_vet_annee ON ct_visite_extra_tarif (vet_annee, ct_visite_extra_id);