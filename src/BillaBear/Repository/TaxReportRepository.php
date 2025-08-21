<?php

/*
 * Copyright Iain Cambridge 2023-2025.
 *
 * Use of this software is governed by the Fair Core License, Version 1.0, ALv2 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace BillaBear\Repository;

use Doctrine\DBAL\Connection;

class TaxReportRepository implements TaxReportRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function getReportItems(array $params = [], ?int $limit = null, ?int $offset = null): iterable
    {
        $sql = "
select i.invoice_number,'invoice' as record_type, il.total as total, il.currency, il.tax_country as tax_country, il.tax_percentage as tax_percentage, il.tax_total as tax_total, il.description as description, il.reverse_charge as reverse_charge, i.paid, i.biller_address_country as invoiced_country,
c.id as customer_id, c.billing_email as customer_email, c.billing_address_country as customer_country, c.tax_number as tax_number, c.\"type\" as customer_type,
tt.id as tax_type_id, tt.\"name\" as tax_type_name
from invoice i 
inner join invoice_line il on il.invoice_id =i.id 
inner join customers c on i.customer_id = c.id
inner join tax_type tt on tt.id  = il.tax_type_id
where i.paid = true
union
select null as invoice_number,'receipt' as record_type, rl.total as total, rl.currency, rl.tax_country as tax_country, rl.vat_percentage as tax_percentage, rl.vat_total as tax_total, rl.description as description, rl.reverse_charge as reverse_charge, true as paid, r.biller_address_country as invoiced_country,
c.id as customer_id, c.billing_email as customer_email, c.billing_address_country as customer_country, c.tax_number as tax_number, c.\"type\" as customer_type,
tt.id as tax_type_id, tt.\"name\" as tax_type_name
from receipt r  
inner join receipt_line rl  on rl.receipt_id =r.id 
inner join customers c on r.customer_id = c.id
inner join tax_type tt on tt.id  = rl.tax_type_id
inner join receipt_payment rp on rp.receipt_id  = r.id 
inner join payment p ON  rp.payment_id = p.id
left join invoice_payment ip ON ip.payment_id = p.id
where ip.invoice_id is null";

        if ($limit) {
            $sql .= ' limit '.$limit.' offset '.$offset;
        }

        $query = $this->connection->prepare($sql);
        $result = $query->executeQuery();
        while ($data = $result->fetchAssociative()) {
            yield $data;
        }
    }

    public function getTaxCollected(string $countryCode, ?\DateTime $since = null): array
    {
        if (!$since) {
            $since = new \DateTime('-12 months');
        }

        $sql = 'select il.currency as currency, sum(il.tax_total) as amount
from invoice i 
inner join invoice_line il on il.invoice_id =i.id 
inner join customers c on i.customer_id = c.id
inner join tax_type tt on tt.id  = il.tax_type_id
where i.paid = true AND il.tax_country = :countryCode AND i.created_at > :since
group by il.currency
union
select rl.currency as currency, sum(rl.vat_total) as amount
from receipt r  
inner join receipt_line rl  on rl.receipt_id =r.id 
inner join customers c on r.customer_id = c.id
inner join tax_type tt on tt.id  = rl.tax_type_id
inner join receipt_payment rp on rp.receipt_id  = r.id 
inner join payment p ON  rp.payment_id = p.id
left join invoice_payment ip ON ip.payment_id = p.id
where ip.invoice_id is null AND rl.tax_country = :countryCode AND r.created_at > :since
group by rl.currency';

        $query = $this->connection->prepare($sql);
        $res = $query->executeQuery(['countryCode' => $countryCode, 'since' => $since->format('Y-m-d')]);

        return $res->fetchAllAssociative();
    }
}
