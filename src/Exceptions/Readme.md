# Error code description

## Error code format
`[ProviderID][ErrorTypeID][SubCode]`


## Providers
| Gateway   | ProviderID |
| --------- | ---------- |
| promotech | `1`       |
| thaibulk  | `2`       |


## Error Types

| Exception Class        | ErrorTypeID |
| ---------------------- | ----------- |
| ConnectionException    | `01`        |
| AuthException          | `02`        |
| ClientException        | `03`        |
| BadResponseException   | `04`        |
| ExternalException      | `05`        |
| InternalException      | `06`        |
| SmsException (generic) | `99`        |

## Example code
| Exception                 | Gateway   | Code     | ความหมาย                                    |
| ------------------------- | --------- | -------- | ------------------------------------------- |
| Connection timeout        | promotech | `10101` | Gateway Promotech เชื่อมต่อไม่ได้ (timeout) |
| Invalid token             | promotech | `10202` | Token หมดอายุ / ผิด                         |
| Missing "success" field   | promotech | `10401` | Response format ผิด ไม่มี `success`         |
| 403 Forbidden จาก gateway | thaibulk  | `20502` | ฝั่ง Thaibulk แจ้ง forbidden                |
| Validate request พัง      | thaibulk  | `20301` | ฝั่งเราส่งข้อมูลไปผิดรูปแบบ                 |
