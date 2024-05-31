import { Component } from "@angular/core";
import { Trip } from "./model";
import { DriversReport } from "./model";
import axios from "axios";

@Component({
  selector: "my-app",
  template: `
    <kendo-grid
      class="k-mb-5"
      [kendoGridBinding]="trips"
      [sortable]="true"
      [filterable]="true"
      [height]="350"
      [loading]="loadTrips"
    >
      <kendo-grid-column field="ID" title="ID"> </kendo-grid-column>
      <kendo-grid-column field="DriverID" title="Driver ID">
      </kendo-grid-column>
      <kendo-grid-column field="Pickup" title="Pickup">
      </kendo-grid-column>
      <kendo-grid-column field="Dropoff" title="Dropoff">
      </kendo-grid-column>
    </kendo-grid>

    <button kendoButton *ngIf="!loadCalulatedTrips" (click)="getCalculatedTrips()"> Calculate Payable Time</button>

    <kendo-grid *ngIf="loadCalulatedTrips" [kendoGridBinding]="calculatedTrips" [sortable]="true" [filterable]="true" [height]="350">
      <kendo-grid-column field="DriverID" title="Driver ID">
      </kendo-grid-column>
      <kendo-grid-column field="PayableTime" title="Payable Time">
      </kendo-grid-column>
    </kendo-grid>
  `,
})
export class AppComponent {
  public trips: Trip[] = [];
  public calculatedTrips: DriversReport[] = [];
  public loadCalulatedTrips = false;
  public loadTrips = true;

  ngOnInit() {
    this.getTrips()
  }

  getTrips() {
    axios.post('/trips').then(response => {
      this.trips = response.data.map((item) => {
        return {
          ID: item.id,
          DriverID: item.driver_id,
          Pickup: new Date(item.pickup).toUTCString(),
          Dropoff: new Date(item.dropoff).toUTCString()
        }
      });
      this.loadTrips = false;
    })
  }

  getCalculatedTrips() {
    axios.post('/trips/calculated').then((response) => {
      let res = response.data
      Object.keys(res).forEach((key) => {
        this.calculatedTrips.push({
          DriverID: parseInt(key),
          PayableTime: res[key]
        })
      });
      this.loadCalulatedTrips = true
    })
  }
}

