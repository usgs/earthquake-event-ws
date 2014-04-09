create or replace view currentProducts as 
select 
    ps.* 
from 
    productSummary ps 
where 
    eventId is not null
    -- newer version of same product
    and not exists (
        select * from productSummary
        where source=ps.source and type=ps.type and code=ps.code and updateTime>ps.updateTime
    );
