export const entityToChar = str => {
  const textarea = document.createElement('textarea');
  textarea.innerHTML = str;
  return textarea.value;
}

export const getScrollBarWidth = () => {
  let outside = document.createElement("div")
  let inside = document.createElement("div")
  outside.style.width = inside.style.width = "100%"
  outside.style.overflow = "scroll"
  document.body.appendChild(outside).appendChild(inside)
  const scrollbar = outside.offsetWidth - inside.offsetWidth
  outside.parentNode.removeChild(outside)
  return scrollbar
}

export const calculateWidths = (columns, viewWidth) => {
  columns.forEach( (col, i) => {
    columns[i].minWidth = col.minWidth ? col.minWidth : col.width
    columns[i].calcWidth = col.minWidth
    columns[i].flexGrow = !(col.minWidth === col.width && col.width === col.maxWidth)
  })

  let minGridWidth = columns.reduce((acc, col) => acc + col.minWidth, 0)

  let flexRemainingSpace = viewWidth - minGridWidth

  while (flexRemainingSpace > 0){

    let flexDenominator = columns.reduce((acc, col) => col.flexGrow ? acc + col.width : acc, 0);
    let flexExpandWidthTotal = 0

    for (let i = 0; i < columns.length; i++) {

      if (columns[i].flexGrow){
        let flexRatio = columns[i].width / flexDenominator
        let flexExpandWidthColumn = flexRatio * flexRemainingSpace
        let calcWidth = columns[i].calcWidth + flexExpandWidthColumn
        columns[i].calcWidth = calcWidth

        // If the calculated width exceeds the max width, use the max width,
        // take the column out of future calculations, and restart the loop
        if (columns[i].maxWidth && ( calcWidth > columns[i].maxWidth ) ) {
          columns[i].flexGrow = false
          columns[i].calcWidth = columns[i].maxWidth
          flexExpandWidthColumn = columns[i].maxWidth - columns[i].minWidth
        }

        flexExpandWidthTotal += flexExpandWidthColumn

      }
    }

    flexRemainingSpace -= flexExpandWidthTotal

  }

  const columnWidths = columns.map((col) => col.calcWidth)

  return {
    minGridWidth,
    columnWidths
  }

}