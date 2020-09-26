import moment from "moment"

const DateCell = ({ cell }) => (
  moment(cell.value).format('LL')
)

export default DateCell