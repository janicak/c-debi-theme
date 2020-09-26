import React, { useMemo, useContext } from "react"
import { useTable, useFlexLayout, useSortBy, usePagination } from "react-table"
import styled, { ThemeContext } from "styled-components"
import { ScrollSync, ScrollSyncPane } from 'react-scroll-sync';

import Th from "./Th"
import Td from "./TD"

const headerProps = (props, { column }) => getStyles(props, column.align)
const cellProps = (props, { cell }) => getStyles(props, cell.column.align)
const getStyles = (props, align = 'left') => [
  props,
  {
    style: {
      justifyContent: align === 'right' ? 'flex-end' : 'flex-start',
      alignItems: 'flex-start',
      display: 'flex',
    },
  },
]

const StyledDiv = styled.div`
  background: white;
  border: 1px solid ${props => props.theme.borderColor};
  box-shadow: 0 1px 1px rgba(0,0,0,.04);
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
  
  .thead-container {
    border-bottom: 1px solid ${props => props.theme.borderColor};
    .thead {
      overflow: hidden;
      color: ${props => props.theme.textColor};
      margin-right: ${props => props.theme.scrollBarWidth}px;
    }
  }
    
  .tbody {
    overflow: auto;
    height: calc(100vh - 240px);
    .tr {
      &.even {
        background-color: white;
      }
      &.odd {
        background-color: #f9f9f9;
      }
    }
  }
  
`
const Table = ({ data }) => {
  const {
    rows,
    prepareRow,
    headerGroups
  } = useTable(
    {
      columns: useMemo(columnSettings, []),
      data: useMemo(() => data, [data.length]),
      defaultColumn: useMemo(() => ({ minWidth: 150, width: 150, maxWidth: 150 }), []),
      autoResetSortBy: false,
    },
    useFlexLayout,
    useSortBy
  )

  return (
    <ScrollSync>
      <StyledDiv className="table">
        <div className="thead-container">
            <ScrollSyncPane group="horizontal">
                <div className="thead">
                  {
                    headerGroups.map(headerGroup => {
                      const trProps = headerGroup.getHeaderGroupProps()
                      const minRowWidth = parseInt(trProps.style.minWidth.replace('px', ''))
                      return (
                        <div {...trProps} className="tr">
                          { headerGroup.headers.map(column => (
                            <Th column={column} headerProps={headerProps} key={column.id} />
                          ))}
                        </div>
                      )
                    })
                  }
                </div>
            </ScrollSyncPane>
        </div>
        <ScrollSyncPane group="horizontal">
          <div className="tbody">
            {
              rows.map(row => {
                prepareRow(row)
                return (
                  <div {...row.getRowProps()} className={`tr ${row.index % 2 ? "even" : "odd"}`}>
                    {
                      row.cells.map(cell => <Td cell={cell} cellProps={cellProps} key={cell.id} />)
                    }
                  </div>
                )
              })
            }
          </div>
        </ScrollSyncPane>
      </StyledDiv>
    </ScrollSync>
  )
}

import ManuscriptCell from "./Cell-publication-manuscript"
import DateCell from "./Cell-date"

const columnSettings = () => [
  {
    Header: "Cont. #",
    accessor: "publication_contribution_number",
    minWidth: 100,
    width: 100,
    maxWidth: 100
  },
  {
    Header: "Type",
    accessor: "publication_type",
    Cell: ({ cell }) => cell.value.name,
    minWidth: 140,
    width: 140,
    maxWidth: 140,
  },
  {
    Header: "Manuscript",
    accessor: "post_title",
    Cell: ManuscriptCell,
    minWidth: 500,
    width: 500,
    maxWidth: 1000
  },
  {
    Header: "Publisher",
    accessor: "publication_publisher_title",
    minWidth: 150,
    width: 150,
    maxWidth: 300
  },
  {
    Header: "Date Published",
    accessor: "publication_date_published",
    Cell: DateCell,
    minWidth: 180,
    width: 180,
    maxWidth: 180,
  },
]

export default Table